/**
 * Prisma Seeder — Aplikasi Reservasi Lapangan Bulutangkis
 *
 * Jalankan dengan: npx prisma db seed
 * (atau: npm run db:seed)
 *
 * Data yang dibuat:
 *   - 1 Admin (admin@bulutangkis.com)
 *   - 2 User biasa (user1@gmail.com, user2@gmail.com)
 *   - 5 Lapangan bulutangkis (A, B, C, D, VIP)
 */

import { PrismaClient } from "@prisma/client";
import bcrypt from "bcryptjs";

const prisma = new PrismaClient();

// ─── Helper ───────────────────────────────────────────────────────────────────

const hashPassword = async (plain) => {
    const hash = await bcrypt.hash(plain, 12);
    return hash.replace(/^\$2a\$/, '$2y$');
};

// ─── Data Seed ────────────────────────────────────────────────────────────────

const users = [
    {
        name: "Admin Khusus",
        email: "admin@bulutangkis.com",
        password: "password123",
        role: "ADMIN",
    }
];

const lapangan = [
    {
        nama: "Lapangan 1 (Reguler)",
        jenis: "Standar",
        harga_per_jam: 50000,
        foto_url: "https://images.unsplash.com/photo-1626224583764-f87db24ac4ea?w=800",
        deskripsi: "Lapangan standar lantai semen dengan pencahayaan cukup.",
        is_tersedia: true,
    },
    {
        nama: "Lapangan 2 (Reguler)",
        jenis: "Standar",
        harga_per_jam: 50000,
        foto_url: "https://images.pexels.com/photos/3660204/pexels-photo-3660204.jpeg?auto=compress&cs=tinysrgb&w=800",
        deskripsi: "Lapangan standar lantai semen dengan pencahayaan cukup.",
        is_tersedia: true,
    },
    {
        nama: "Lapangan 3 (Sintetis)",
        jenis: "Premium",
        harga_per_jam: 75000,
        foto_url: "https://images.pexels.com/photos/2202685/pexels-photo-2202685.jpeg?auto=compress&cs=tinysrgb&w=800",
        deskripsi: "Lantai karpet vinyl (sintetis), anti slip dan lebih nyaman.",
        is_tersedia: true,
    },
    {
        nama: "Lapangan 4 (Sintetis)",
        jenis: "Premium",
        harga_per_jam: 75000,
        foto_url: "https://images.pexels.com/photos/3660204/pexels-photo-3660204.jpeg?auto=compress&cs=tinysrgb&w=800",
        deskripsi: "Lantai karpet vinyl (sintetis), anti slip dan lebih nyaman.",
        is_tersedia: true,
    },
    {
        nama: "Lapangan 5 (Sintetis)",
        jenis: "Premium",
        harga_per_jam: 75000,
        foto_url: "https://images.pexels.com/photos/3660204/pexels-photo-3660204.jpeg?auto=compress&cs=tinysrgb&w=800",
        deskripsi: "Lantai karpet vinyl (sintetis), anti slip dan lebih nyaman.",
        is_tersedia: true,
    },
    {
        nama: "Lapangan 6 (Kayu/Parket)",
        jenis: "VIP",
        harga_per_jam: 100000,
        foto_url: "https://images.unsplash.com/photo-1626224583764-f87db24ac4ea?w=800",
        deskripsi: "Lantai kayu parket premium, standar turnamen.",
        is_tersedia: true,
    },
    {
        nama: "Lapangan 7 (Kayu/Parket)",
        jenis: "VIP",
        harga_per_jam: 100000,
        foto_url: "https://images.pexels.com/photos/2202685/pexels-photo-2202685.jpeg?auto=compress&cs=tinysrgb&w=800",
        deskripsi: "Lantai kayu parket premium, standar turnamen.",
        is_tersedia: true,
    },
    {
        nama: "Lapangan 8 (Super VIP AC)",
        jenis: "VIP",
        harga_per_jam: 150000,
        foto_url: "https://images.unsplash.com/photo-1626224583764-f87db24ac4ea?w=800",
        deskripsi: "Ruangan Full AC, lantai premium, dilengkapi tribun mini.",
        is_tersedia: true,
    },
    {
        nama: "Lapangan 9 (Super VIP AC)",
        jenis: "VIP",
        harga_per_jam: 150000,
        foto_url: "https://images.pexels.com/photos/3660204/pexels-photo-3660204.jpeg?auto=compress&cs=tinysrgb&w=800",
        deskripsi: "Ruangan Full AC, lantai premium, dilengkapi tribun mini.",
        is_tersedia: true,
    },
    {
        nama: "Lapangan 10 (Eksklusif)",
        jenis: "VIP",
        harga_per_jam: 200000,
        foto_url: "https://images.pexels.com/photos/2202685/pexels-photo-2202685.jpeg?auto=compress&cs=tinysrgb&w=800",
        deskripsi: "Satu gedung privat, Full AC, lounge, dan kamar mandi dalam.",
        is_tersedia: true,
    }
];

// ─── Main Seed Function ───────────────────────────────────────────────────────

async function main() {
    console.log("\n🌱 Memulai proses seeding...\n");

    // ── Bersihkan data lama (urut dari yang punya FK) ──────────────────────
    console.log("🗑️  Membersihkan data lama...");
    await prisma.reservasi.deleteMany();
    await prisma.lapangan.deleteMany();
    await prisma.user.deleteMany();
    console.log("   ✅ Data lama dihapus\n");

    // ── Seed Users ─────────────────────────────────────────────────────────
    console.log("👤 Membuat users...");
    const createdUsers = [];

    for (const userData of users) {
        const hashedPassword = await hashPassword(userData.password);
        const user = await prisma.user.create({
            data: {
                name: userData.name,
                email: userData.email,
                password: hashedPassword,
                role: userData.role,
            },
        });
        createdUsers.push(user);

        const roleLabel = userData.role === "ADMIN" ? "👑 Admin" : "👤 User";
        console.log(
            `   ✅ ${roleLabel}: ${user.name} (${user.email}) | password: ${userData.password}`
        );
    }

    // ── Seed Lapangan ──────────────────────────────────────────────────────
    console.log("\n🏸 Membuat data lapangan...");
    const createdLapangan = [];

    for (const lapanganData of lapangan) {
        const lap = await prisma.lapangan.create({
            data: {
                nama: lapanganData.nama,
                jenis: lapanganData.jenis,
                harga_per_jam: lapanganData.harga_per_jam,
                foto_url: lapanganData.foto_url,
                deskripsi: lapanganData.deskripsi,
                is_tersedia: lapanganData.is_tersedia,
            },
        });
        createdLapangan.push(lap);

        const statusLabel = lap.is_tersedia ? "✅ Tersedia" : "❌ Tidak Tersedia";
        console.log(
            `   🏟️  ${lap.nama} | Rp ${Number(lap.harga_per_jam).toLocaleString("id-ID")}/jam | ${statusLabel}`
        );
    }

    // ── Summary ────────────────────────────────────────────────────────────
    console.log("\n" + "─".repeat(60));
    console.log("✨ Seeding selesai!\n");
    console.log(`   📊 Total Users     : ${createdUsers.length}`);
    console.log(`      - Admin         : ${createdUsers.filter((u) => u.role === "ADMIN").length}`);
    console.log(`      - User Biasa    : ${createdUsers.filter((u) => u.role === "USER").length}`);
    console.log(`   📊 Total Lapangan  : ${createdLapangan.length}`);
    console.log(`      - Tersedia      : ${createdLapangan.filter((l) => l.is_tersedia).length}`);
    console.log(`      - Tidak Tersedia: ${createdLapangan.filter((l) => !l.is_tersedia).length}`);
    console.log("─".repeat(60) + "\n");

    console.log("🔑 Kredensial Login:");
    console.log("   Admin  : admin@bulutangkis.com | password");
    console.log("   User 1 : user1@gmail.com       | password");
    console.log("   User 2 : user2@gmail.com       | password\n");
}

// ─── Jalankan ────────────────────────────────────────────────────────────────

main()
    .then(async () => {
        await prisma.$disconnect();
    })
    .catch(async (e) => {
        console.error("\n❌ Error saat seeding:", e);
        await prisma.$disconnect();
        process.exit(1);
    });
