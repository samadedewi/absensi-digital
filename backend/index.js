const express = require('express');
const { PrismaClient } = require('@prisma/client');
const cors = require('cors');
require('dotenv').config();

const app = express();
const prisma = new PrismaClient();
const PORT = process.env.PORT || 5000;

app.use(cors());
app.use(express.json());

app.get('/', (req, res) => {
  res.json({ message: 'Absensi Digital API is running' });
});

// Example route for Prisma
app.get('/test-db', async (req, res) => {
  try {
    // This will fail if DATABASE_URL is not set correctly
    const result = await prisma.$queryRaw`SELECT 1 as result`;
    res.json({ status: 'connected', result });
  } catch (error) {
    res.status(500).json({ status: 'error', message: error.message });
  }
});

app.listen(PORT, () => {
  console.log(`Server is running on http://localhost:${PORT}`);
});
