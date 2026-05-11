# Menggunakan Python 3.11 yang stabil
FROM python:3.11-slim

# Instal dependensi sistem yang dibutuhkan untuk OpenCV dan Flask
RUN apt-get update && apt-get install -y \
    libgl1-mesa-glx \
    libglib2.0-0 \
    libsm6 \
    libxext6 \
    libxrender-dev \
    && rm -rf /var/lib/apt/lists/*

# Set working directory
WORKDIR /app

# Salin file requirements dan instal library Python
COPY requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt

# Salin semua kode ke dalam container
COPY . .

# Ekspos port yang digunakan Flask
EXPOSE 5001

# Jalankan aplikasi menggunakan Gunicorn untuk produksi (lebih stabil daripada app.py langsung)
CMD ["python", "app.py"]
