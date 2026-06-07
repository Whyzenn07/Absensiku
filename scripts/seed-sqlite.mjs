/**
 * Creates a pre-seeded SQLite database for Vercel deployment.
 * Run: node scripts/seed-sqlite.mjs
 */
import initSqlJs from 'sql.js';
import bcrypt from 'bcryptjs';
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const dbPath = path.join(__dirname, '..', 'database', 'database.sqlite');

const SQL = await initSqlJs();
const db = new SQL.Database();

const now = new Date().toISOString().replace('T', ' ').substring(0, 19);
// bcryptjs uses $2b$ prefix; PHP expects $2y$ (functionally identical)
const hash = bcrypt.hashSync('password', 10).replace(/^\$2b\$/, '$2y$');

// ── Schema ────────────────────────────────────────────────────────────────────
db.run(`
CREATE TABLE migrations (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  migration TEXT NOT NULL,
  batch INTEGER NOT NULL
);
`);

db.run(`
CREATE TABLE users (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name TEXT NOT NULL,
  email TEXT UNIQUE NOT NULL,
  email_verified_at DATETIME,
  password TEXT NOT NULL,
  role TEXT DEFAULT 'mahasiswa',
  created_at DATETIME,
  updated_at DATETIME
);
`);

db.run(`
CREATE TABLE password_reset_tokens (
  email TEXT PRIMARY KEY,
  token TEXT NOT NULL,
  created_at DATETIME
);
`);

db.run(`
CREATE TABLE sessions (
  id TEXT PRIMARY KEY,
  user_id INTEGER,
  ip_address TEXT,
  user_agent TEXT,
  payload TEXT NOT NULL,
  last_activity INTEGER NOT NULL
);
`);
db.run(`CREATE INDEX sessions_user_id_index ON sessions(user_id);`);
db.run(`CREATE INDEX sessions_last_activity_index ON sessions(last_activity);`);

db.run(`
CREATE TABLE cache (
  key TEXT PRIMARY KEY,
  value TEXT NOT NULL,
  expiration INTEGER NOT NULL
);
`);

db.run(`
CREATE TABLE cache_locks (
  key TEXT PRIMARY KEY,
  owner TEXT NOT NULL,
  expiration INTEGER NOT NULL
);
`);

db.run(`
CREATE TABLE jobs (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  queue TEXT NOT NULL,
  payload TEXT NOT NULL,
  attempts INTEGER NOT NULL,
  reserved_at INTEGER,
  available_at INTEGER NOT NULL,
  created_at INTEGER NOT NULL
);
`);
db.run(`CREATE INDEX jobs_queue_index ON jobs(queue);`);

db.run(`
CREATE TABLE prodis (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  nama TEXT UNIQUE NOT NULL,
  created_at DATETIME,
  updated_at DATETIME
);
`);

db.run(`
CREATE TABLE kelas (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  nama TEXT UNIQUE NOT NULL,
  created_at DATETIME,
  updated_at DATETIME
);
`);

db.run(`
CREATE TABLE mahasiswas (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
  nim TEXT UNIQUE NOT NULL,
  no_hp TEXT,
  prodi_id INTEGER NOT NULL REFERENCES prodis(id) ON DELETE RESTRICT,
  kelas_id INTEGER NOT NULL REFERENCES kelas(id) ON DELETE RESTRICT,
  created_at DATETIME,
  updated_at DATETIME
);
`);

db.run(`
CREATE TABLE mata_kuliahs (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  nama TEXT NOT NULL,
  kode TEXT UNIQUE NOT NULL,
  user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
  created_at DATETIME,
  updated_at DATETIME
);
`);

db.run(`
CREATE TABLE jadwals (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  mata_kuliah_id INTEGER NOT NULL REFERENCES mata_kuliahs(id) ON DELETE CASCADE,
  kelas_id INTEGER NOT NULL REFERENCES kelas(id) ON DELETE CASCADE,
  hari TEXT NOT NULL CHECK(hari IN ('senin','selasa','rabu','kamis','jumat','sabtu')),
  jam_mulai TEXT NOT NULL,
  jam_selesai TEXT NOT NULL,
  created_at DATETIME,
  updated_at DATETIME
);
`);

db.run(`
CREATE TABLE sesis (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  mata_kuliah_id INTEGER NOT NULL REFERENCES mata_kuliahs(id) ON DELETE CASCADE,
  kelas_id INTEGER NOT NULL REFERENCES kelas(id) ON DELETE CASCADE,
  user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
  token TEXT UNIQUE NOT NULL,
  qr_data TEXT NOT NULL,
  durasi INTEGER NOT NULL,
  status TEXT DEFAULT 'aktif' CHECK(status IN ('aktif','selesai')),
  started_at DATETIME NOT NULL,
  ended_at DATETIME,
  created_at DATETIME,
  updated_at DATETIME
);
`);

db.run(`
CREATE TABLE absensis (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  sesi_id INTEGER NOT NULL REFERENCES sesis(id) ON DELETE CASCADE,
  mahasiswa_id INTEGER NOT NULL REFERENCES mahasiswas(id) ON DELETE CASCADE,
  status TEXT DEFAULT 'alpha' CHECK(status IN ('hadir','izin','alpha')),
  waktu_scan DATETIME,
  created_at DATETIME,
  updated_at DATETIME,
  UNIQUE(sesi_id, mahasiswa_id)
);
`);

// ── Migration tracking ────────────────────────────────────────────────────────
const migrations = [
  '0001_01_01_000000_create_users_table',
  '0001_01_01_000001_create_cache_table',
  '0001_01_01_000002_create_jobs_table',
  '2026_04_27_120000_create_prodis_table',
  '2026_04_27_140000_drop_remember_token_from_users_table',
  '2026_04_27_043517_create_mahasiswas_table',
  '2026_06_06_000001_create_kelas_table',
  '2026_06_06_000002_recreate_mahasiswas_table',
  '2026_06_06_000003_create_mata_kuliahs_table',
  '2026_06_06_000004_create_jadwals_table',
  '2026_06_06_000005_create_sesis_table',
  '2026_06_06_000006_create_absensis_table',
];
migrations.forEach((m, i) => db.run(
  `INSERT INTO migrations (migration, batch) VALUES (?, 1)`, [m]
));

// ── Seed data ─────────────────────────────────────────────────────────────────

// Admin / Dosen
db.run(`INSERT INTO users (name, email, password, role, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?)`,
  ['Dr. Wayy Pratama', 'admin@telkom-university.ac.id', hash, 'admin', now, now]);
const adminId = db.exec('SELECT last_insert_rowid()')[0].values[0][0];

// Prodis
const prodiNames = ['Informatika', 'Sistem Informasi', 'Teknik Komputer', 'Teknik Elektro'];
prodiNames.forEach(nama => db.run(
  `INSERT INTO prodis (nama, created_at, updated_at) VALUES (?, ?, ?)`, [nama, now, now]
));
const prodis = {};
db.exec('SELECT id, nama FROM prodis').forEach(r => r.values.forEach(([id, nama]) => prodis[nama] = id));

// Kelas
const kelasNames = ['IF-01', 'IF-02', 'SI-01', 'SI-02', 'TI-01', 'TI-02'];
kelasNames.forEach(nama => db.run(
  `INSERT INTO kelas (nama, created_at, updated_at) VALUES (?, ?, ?)`, [nama, now, now]
));
const kelas = {};
db.exec('SELECT id, nama FROM kelas').forEach(r => r.values.forEach(([id, nama]) => kelas[nama] = id));

// Mata Kuliah
const mkData = [
  ['Pemrograman Web', 'IF101'], ['Basis Data', 'IF102'], ['Jaringan Komputer', 'IF103'],
  ['Sistem Operasi', 'IF104'], ['Algoritma & Pemrograman', 'IF105'], ['Rekayasa Perangkat Lunak', 'IF106'],
];
mkData.forEach(([nama, kode]) => db.run(
  `INSERT INTO mata_kuliahs (nama, kode, user_id, created_at, updated_at) VALUES (?, ?, ?, ?, ?)`,
  [nama, kode, adminId, now, now]
));
const mks = db.exec('SELECT id FROM mata_kuliahs ORDER BY id').map(r => r.values.map(v => v[0]))[0];

// Jadwal
const jadwalData = [
  [mks[0], kelas['IF-01'], 'senin',  '08:00', '09:40'],
  [mks[1], kelas['IF-01'], 'selasa', '10:30', '12:10'],
  [mks[2], kelas['IF-01'], 'rabu',   '13:00', '14:40'],
  [mks[3], kelas['IF-02'], 'kamis',  '08:00', '09:40'],
  [mks[4], kelas['IF-02'], 'jumat',  '10:30', '12:10'],
  [mks[5], kelas['SI-01'], 'senin',  '13:00', '14:40'],
];
jadwalData.forEach(([mkId, kId, hari, mulai, selesai]) => db.run(
  `INSERT INTO jadwals (mata_kuliah_id, kelas_id, hari, jam_mulai, jam_selesai, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?)`,
  [mkId, kId, hari, mulai, selesai, now, now]
));

// Mahasiswa
const mahasiswaData = [
  { name: 'Neilsya Putri',    email: 'neilsyaputri@student.telkom.ac.id', nim: '103042310048', prodi: 'Informatika',      kel: 'IF-02' },
  { name: 'Rivaldo Tandoko',  email: 'rivaldo@student.telkom.ac.id',       nim: '1034217006',   prodi: 'Informatika',      kel: 'IF-02' },
  { name: 'Wahyu Pratama',    email: 'wahyupratama@student.telkom.ac.id',  nim: '1034217008',   prodi: 'Informatika',      kel: 'IF-02' },
  { name: 'Aiqbal Hermawan',  email: 'aiqbal@student.telkom.ac.id',        nim: '1034219030',   prodi: 'Informatika',      kel: 'IF-02' },
  { name: 'Wahyu Argo Mulyo', email: 'wahyuargomu123@gmail.com',           nim: '1034219045',   prodi: 'Sistem Informasi', kel: 'SI-01' },
  { name: 'Muhammad Farhan',  email: 'farhan@student.telkom.ac.id',        nim: '1034221008',   prodi: 'Informatika',      kel: 'IF-01' },
];

const mahasiswaIds = [];
mahasiswaData.forEach(({ name, email, nim, prodi, kel }) => {
  db.run(`INSERT INTO users (name, email, password, role, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?)`,
    [name, email, hash, 'mahasiswa', now, now]);
  const userId = db.exec('SELECT last_insert_rowid()')[0].values[0][0];
  db.run(`INSERT INTO mahasiswas (user_id, nim, no_hp, prodi_id, kelas_id, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?)`,
    [userId, nim, '085000000000', prodis[prodi], kelas[kel], now, now]);
  mahasiswaIds.push(db.exec('SELECT last_insert_rowid()')[0].values[0][0]);
});

// Sample sesi + absensi (last 5 days)
const statusOptions = ['hadir', 'hadir', 'hadir', 'hadir', 'izin', 'alpha'];
const mkArr = [mks[0], mks[1], mks[2], mks[3], mks[4]];

for (let i = 5; i >= 1; i--) {
  const d = new Date(Date.now() - i * 86400000);
  const dateStr = d.toISOString().replace('T', ' ').substring(0, 19);
  const mk = mkArr[i % mkArr.length];
  const token = Math.random().toString(36).substring(2, 10).toUpperCase();
  const qrData = `qr-${Date.now()}-${i}`;

  db.run(`INSERT INTO sesis (mata_kuliah_id, kelas_id, user_id, token, qr_data, durasi, status, started_at, ended_at, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)`,
    [mk, kelas['IF-02'], adminId, token, qrData, 90, 'selesai',
     `${d.toISOString().substring(0, 10)} 08:00:00`,
     `${d.toISOString().substring(0, 10)} 09:30:00`,
     dateStr, dateStr]);

  const sesiId = db.exec('SELECT last_insert_rowid()')[0].values[0][0];

  mahasiswaIds.slice(0, 4).forEach((mhsId, j) => {
    const status = statusOptions[(i + j) % statusOptions.length];
    db.run(`INSERT INTO absensis (sesi_id, mahasiswa_id, status, waktu_scan, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?)`,
      [sesiId, mhsId, status, `${d.toISOString().substring(0, 10)} 08:0${j}:00`, dateStr, dateStr]);
  });
}

// ── Write to file ─────────────────────────────────────────────────────────────
const data = db.export();
fs.writeFileSync(dbPath, Buffer.from(data));
console.log(`✅ SQLite database created at ${dbPath} (${(data.length / 1024).toFixed(1)} KB)`);
db.close();
