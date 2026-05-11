"""
Face Verification API — Sistem Absensi
Menggunakan OpenCV LBPH Face Recognizer (tanpa TensorFlow/DeepFace).
Kompatibel dengan Python 3.9+ termasuk Python 3.14.

Endpoint:
  GET  /health    → cek status server
  POST /verify    → verifikasi wajah selfie vs foto referensi
"""

import os
import base64
import logging
import tempfile
import json
from io import BytesIO

import cv2
import numpy as np
from PIL import Image
from flask import Flask, request, jsonify
from flask_cors import CORS

# ──────────────────────────────────────────────
# Konfigurasi
# ──────────────────────────────────────────────
app = Flask(__name__)
CORS(app)

logging.basicConfig(level=logging.INFO, format="%(asctime)s %(levelname)s %(message)s")
logger = logging.getLogger(__name__)

LARAVEL_ROOT  = os.path.abspath(os.path.join(os.path.dirname(__file__), ".."))
THRESHOLD     = float(os.environ.get("FACE_THRESHOLD", "80"))  # LBPH: semakin KECIL = semakin mirip

# Load Haar Cascade untuk deteksi wajah
CASCADE_PATH = cv2.data.haarcascades + "haarcascade_frontalface_default.xml"
face_cascade = cv2.CascadeClassifier(CASCADE_PATH)


# ──────────────────────────────────────────────
# Helper
# ──────────────────────────────────────────────
def base64_to_cv2(b64_string: str) -> np.ndarray:
    """Ubah base64 → numpy array BGR (format OpenCV)."""
    if "," in b64_string:
        b64_string = b64_string.split(",", 1)[1]
    img_bytes = base64.b64decode(b64_string)
    img_pil   = Image.open(BytesIO(img_bytes)).convert("RGB")
    img_np    = np.array(img_pil)
    return cv2.cvtColor(img_np, cv2.COLOR_RGB2BGR)


def file_to_cv2(path: str) -> np.ndarray:
    """Baca file gambar → numpy array BGR."""
    img = cv2.imread(path)
    if img is None:
        raise FileNotFoundError(f"Tidak dapat membaca gambar: {path}")
    return img


def detect_and_crop_face(img_bgr: np.ndarray, label: str = ""):
    """
    Deteksi wajah menggunakan Haar Cascade.
    Kembalikan gambar wajah yang di-crop & di-resize ke 200x200 grayscale,
    atau None jika tidak ada wajah ditemukan.
    """
    gray   = cv2.cvtColor(img_bgr, cv2.COLOR_BGR2GRAY)
    # Equalize histogram agar pencahayaan tidak terlalu mempengaruhi
    gray   = cv2.equalizeHist(gray)

    faces  = face_cascade.detectMultiScale(
        gray,
        scaleFactor = 1.1,
        minNeighbors= 5,
        minSize     = (60, 60),
        flags       = cv2.CASCADE_SCALE_IMAGE
    )

    if len(faces) == 0:
        logger.warning(f"Tidak ada wajah terdeteksi [{label}]")
        return None

    # Ambil wajah terbesar (kemungkinan wajah utama)
    x, y, w, h = max(faces, key=lambda f: f[2] * f[3])
    face_gray   = gray[y:y+h, x:x+w]
    face_resized= cv2.resize(face_gray, (200, 200))
    return face_resized


def resolve_reference_path(relative_path: str) -> str:
    """Ubah path relatif storage Laravel → path absolut."""
    relative_path = relative_path.lstrip("/")
    if relative_path.startswith("storage/"):
        relative_path = relative_path[len("storage/"):]
    full_path = os.path.join(LARAVEL_ROOT, "storage", "app", "public", relative_path)
    return os.path.normpath(full_path)


def confidence_label(distance: float) -> str:
    """Ubah nilai distance LBPH ke label kepercayaan."""
    if distance < 40:
        return "Sangat Tinggi"
    elif distance < 60:
        return "Tinggi"
    elif distance < THRESHOLD:
        return "Cukup"
    else:
        return "Rendah"


# ──────────────────────────────────────────────
# Routes
# ──────────────────────────────────────────────
@app.route("/health", methods=["GET"])
def health():
    return jsonify({
        "status"    : "ok",
        "engine"    : "OpenCV LBPH",
        "threshold" : THRESHOLD,
        "cascade"   : os.path.basename(CASCADE_PATH),
    })


@app.route("/verify", methods=["POST"])
def verify():
    """
    Request JSON:
      {
        "reference_path" : "students/foto.jpg",
        "selfie_base64"  : "data:image/jpeg;base64,..."
      }
    """
    data = request.get_json(silent=True)
    if not data:
        return jsonify({"error": "Request harus berformat JSON"}), 400

    reference_path = data.get("reference_path")
    selfie_b64     = data.get("selfie_base64")

    if not reference_path or not selfie_b64:
        return jsonify({"error": "Field 'reference_path' dan 'selfie_base64' wajib diisi"}), 400

    # ── 1. Resolve & baca foto referensi ──
    abs_reference = resolve_reference_path(reference_path)
    logger.info(f"Reference: {abs_reference}")

    if not os.path.isfile(abs_reference):
        return jsonify({
            "error" : f"Foto referensi tidak ditemukan: {abs_reference}",
            "hint"  : "Pastikan mahasiswa sudah upload foto saat registrasi",
        }), 404

    try:
        ref_img = file_to_cv2(abs_reference)
    except Exception as e:
        return jsonify({"error": f"Gagal membaca foto referensi: {str(e)}"}), 500

    # ── 2. Decode selfie ──
    try:
        selfie_img = base64_to_cv2(selfie_b64)
    except Exception as e:
        return jsonify({"error": "Format gambar selfie tidak valid"}), 400

    # ── 3. Deteksi wajah di kedua gambar ──
    ref_face    = detect_and_crop_face(ref_img,    label="referensi")
    selfie_face = detect_and_crop_face(selfie_img, label="selfie")

    if ref_face is None:
        return jsonify({
            "verified"   : False,
            "distance"   : None,
            "confidence" : "Tidak Diketahui",
            "message"    : "Wajah tidak terdeteksi pada foto referensi mahasiswa. Minta mahasiswa upload ulang foto yang lebih jelas.",
            "error_type" : "ref_face_not_detected",
        })

    if selfie_face is None:
        return jsonify({
            "verified"   : False,
            "distance"   : None,
            "confidence" : "Tidak Diketahui",
            "message"    : "Wajah tidak terdeteksi pada selfie. Pastikan wajah terlihat jelas dan pencahayaan cukup.",
            "error_type" : "selfie_face_not_detected",
        })

    # ── 4. Training LBPH dengan foto referensi ──
    recognizer = cv2.face.LBPHFaceRecognizer_create()
    recognizer.train([ref_face], np.array([0]))  # label 0 = mahasiswa ini

    # ── 5. Prediksi selfie ──
    label, distance = recognizer.predict(selfie_face)
    distance        = round(float(distance), 2)
    verified        = distance < THRESHOLD

    logger.info(f"Distance: {distance} | Threshold: {THRESHOLD} | Verified: {verified}")

    return jsonify({
        "verified"   : verified,
        "distance"   : distance,
        "threshold"  : THRESHOLD,
        "confidence" : confidence_label(distance),
        "message"    : "Wajah cocok! Identitas terverifikasi." if verified else "Wajah tidak cocok. Absensi ditolak.",
    })


# ──────────────────────────────────────────────
# Entry Point
# ──────────────────────────────────────────────
if __name__ == "__main__":
    port = int(os.environ.get("PORT", 5001))
    logger.info(f"Face API (OpenCV LBPH) berjalan di http://127.0.0.1:{port}")
    logger.info(f"Threshold: {THRESHOLD} | Cascade: {CASCADE_PATH}")
    app.run(host="127.0.0.1", port=port, debug=False)
