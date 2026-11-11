@extends('layouts.app')

@section('title', 'Syarat dan Ketentuan — GameNexa')

@section('styles')
<style>
    .legal-wrapper {
        max-width: 900px;
        margin: 20px auto 50px;
    }

    .legal-card {
        background: var(--bg-card);
        border: 1px solid var(--border-glass);
        border-radius: var(--radius-lg);
        padding: 38px;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.4), 0 0 30px rgba(168, 85, 247, 0.1);
    }

    .legal-title {
        font-family: var(--font-heading);
        font-size: 30px;
        font-weight: 900;
        color: #fff;
        margin-bottom: 8px;
    }

    .legal-updated,
    .legal-intro,
    .legal-section p,
    .legal-section li,
    .legal-notice {
        color: var(--text-muted);
        line-height: 1.75;
    }

    .legal-updated {
        margin-bottom: 30px;
        font-size: 14px;
    }

    .legal-intro {
        margin-bottom: 30px;
    }

    .legal-section {
        margin-bottom: 26px;
    }

    .legal-section h2 {
        color: #fff;
        font-family: var(--font-heading);
        font-size: 18px;
        margin-bottom: 10px;
    }

    .legal-section ul {
        padding-left: 22px;
    }

    .legal-notice {
        border-top: 1px solid var(--border-glass);
        padding-top: 24px;
    }

    @media (max-width: 600px) {
        .legal-card {
            padding: 24px;
        }
    }
</style>
@endsection

@section('content')
<div class="container">
    <div class="legal-wrapper">
        <article class="legal-card">
            <h1 class="legal-title">Syarat dan Ketentuan</h1>
            <p class="legal-updated">Berlaku mulai 16 Oktober 2026</p>

            <p class="legal-intro">
                Selamat datang di GameNexa. Dengan mengakses situs dan menggunakan layanan kami,
                Anda menyatakan telah membaca, memahami, dan menyetujui syarat serta ketentuan
                berikut. Mohon periksa kembali data pesanan sebelum pembayaran dilakukan.
            </p>

            <section class="legal-section">
                <h2>1. Ruang Lingkup Layanan</h2>
                <p>
                    GameNexa menyediakan layanan pembelian produk digital, termasuk top-up mata uang
                    game, voucher, dan produk digital lain yang tersedia di situs. Pemrosesan pesanan
                    dilakukan secara otomatis melalui sistem GameNexa dan mitra penyedia produk digital.
                </p>
            </section>

            <section class="legal-section">
                <h2>2. Persetujuan Pengguna</h2>
                <p>
                    Penggunaan situs, pengisian formulir pesanan, atau pembayaran atas suatu pesanan
                    dianggap sebagai persetujuan terhadap ketentuan ini. Apabila Anda tidak menyetujui
                    salah satu bagian dari ketentuan ini, mohon tidak melanjutkan penggunaan layanan.
                </p>
            </section>

            <section class="legal-section">
                <h2>3. Pemesanan dan Kebenaran Data</h2>
                <p>
                    Pengguna tidak wajib membuat akun untuk melakukan pemesanan. Namun, pengguna wajib
                    mengisi data yang diperlukan secara benar, lengkap, dan sesuai dengan akun game atau
                    tujuan pengiriman produk digital.
                </p>
                <ul>
                    <li>Periksa kembali User ID, Zone ID, server, nomor tujuan, dan pilihan produk sebelum membayar.</li>
                    <li>GameNexa tidak bertanggung jawab atas transaksi yang berhasil dikirim ke data yang salah akibat kesalahan pengguna.</li>
                    <li>Pesanan yang sudah diproses atau produk digital yang sudah terkirim umumnya tidak dapat diubah atau dibatalkan.</li>
                    <li>GameNexa dapat menolak atau menunda pesanan yang terindikasi tidak wajar, salah harga, atau berpotensi melanggar hukum.</li>
                </ul>
            </section>

            <section class="legal-section">
                <h2>4. Harga dan Pembayaran</h2>
                <p>
                    Harga yang ditampilkan adalah harga yang berlaku pada saat pesanan dibuat dan dapat
                    berubah sewaktu-waktu. Pesanan dianggap masuk ke tahap pemrosesan setelah pembayaran
                    berhasil dikonfirmasi oleh penyedia pembayaran.
                </p>
                <ul>
                    <li>Pengguna harus menyelesaikan pembayaran melalui metode yang tersedia pada halaman pembayaran.</li>
                    <li>GameNexa tidak bertanggung jawab atas biaya tambahan dari bank, dompet digital, atau penyedia pembayaran.</li>
                    <li>Jika terjadi perbedaan status pembayaran, status pada sistem pembayaran dan hasil pengecekan GameNexa menjadi dasar pemeriksaan.</li>
                </ul>
            </section>

            <section class="legal-section">
                <h2>5. Ketentuan Sistem Pembayaran (Doit.id Payment Gateway)</h2>
                <p>
                    GameNexa menggunakan Doit.id sebagai penyedia layanan payment gateway resmi 
                    untuk memproses pembayaran. Dengan melakukan pembayaran melalui sistem kami, Anda juga menyetujui 
                    ketentuan layanan dari Doit.id.
                </p>
                <ul>
                    <li><strong>Keamanan Transaksi:</strong> Semua transaksi pembayaran dilindungi dengan enkripsi SSL/TLS dan standar keamanan PCI-DSS Level 1.</li>
                    <li><strong>Data Pembayaran:</strong> Detail pembayaran Anda diproses langsung oleh Doit.id dan tidak disimpan di server GameNexa.</li>
                    <li><strong>Metode Pembayaran:</strong> Tersedia berbagai metode pembayaran termasuk kartu kredit/debit, virtual account, e-wallet (GoPay, OVO, DANA, ShopeePay), dan convenience store (Alfamart, Indomaret).</li>
                    <li><strong>Konfirmasi Pembayaran:</strong> Status pembayaran akan diupdate secara real-time melalui sistem webhook Doit.id.</li>
                    <li><strong>Dispute & Chargeback:</strong> Jika terjadi sengketa pembayaran, GameNexa akan bekerjasama dengan Doit.id sesuai prosedur yang berlaku.</li>
                    <li><strong>Biaya Tambahan:</strong> Beberapa metode pembayaran mungkin dikenakan biaya admin oleh bank atau penyedia layanan pembayaran yang bersangkutan.</li>
                    <li><strong>Pembayaran Tertunda:</strong> Untuk metode pembayaran yang memerlukan konfirmasi manual (seperti transfer bank), mohon selesaikan pembayaran sebelum masa berlaku expired.</li>
                    <li><strong>Kepatuhan Regulasi:</strong> Doit.id memproses pembayaran sesuai ketentuan dan regulasi yang berlaku.</li>
                </ul>
                <p>
                    <strong>Informasi lebih lanjut:</strong> Untuk detail lengkap mengenai keamanan dan ketentuan Doit.id, 
                    kunjungi <a href="https://doit.id/docs/" target="_blank" rel="noopener noreferrer">doit.id/docs</a>.
                </p>
            </section>

            <section class="legal-section">
                <h2>6. Proses Pengiriman Produk Digital</h2>
                <p>
                    Pesanan biasanya diproses dalam waktu 1–5 menit setelah pembayaran terkonfirmasi.
                    Waktu tersebut merupakan estimasi dan dapat berubah karena antrean sistem, gangguan
                    jaringan, pemeliharaan, atau kendala pada game developer maupun provider.
                </p>
                <p>
                    Pengguna dapat menggunakan nomor pesanan untuk memeriksa status transaksi melalui
                    halaman pelacakan yang tersedia di situs.
                </p>
            </section>

            <section class="legal-section">
                <h2>7. Pembatalan dan Pengembalian Dana</h2>
                <p>
                    Karena produk yang dijual bersifat digital dan dapat langsung diproses, pembatalan
                    tidak dapat dilakukan setelah produk berhasil dikirim. Pengembalian dana dapat
                    dipertimbangkan setelah pemeriksaan apabila:
                </p>
                <ul>
                    <li>pembayaran terpotong lebih dari satu kali untuk pesanan yang sama;</li>
                    <li>pesanan gagal diproses karena kesalahan sistem GameNexa atau provider; atau</li>
                    <li>produk tidak diterima setelah melewati waktu pemeriksaan yang wajar dan pengguna memberikan bukti yang diperlukan.</li>
                </ul>
                <p>
                    Pengembalian dana tidak berlaku untuk kesalahan User ID atau data tujuan dari pengguna,
                    akun game yang bermasalah atau terkena sanksi developer, perubahan keinginan setelah
                    pembayaran, maupun keterlambatan yang disebabkan oleh game developer atau provider.
                    Setiap permintaan refund akan diperiksa berdasarkan riwayat transaksi dan bukti pembayaran.
                </p>
            </section>

            <section class="legal-section">
                <h2>8. Tanggung Jawab Pengguna</h2>
                <p>Pengguna bertanggung jawab untuk:</p>
                <ul>
                    <li>memastikan data tujuan dan rincian produk sebelum pembayaran;</li>
                    <li>menjaga keamanan bukti pembayaran dan informasi pesanan;</li>
                    <li>menggunakan layanan hanya untuk tujuan yang sah;</li>
                    <li>tidak melakukan penipuan, manipulasi pembayaran, chargeback palsu, atau penyalahgunaan sistem; dan</li>
                    <li>mematuhi ketentuan penggunaan dari game developer atau penerbit produk terkait.</li>
                </ul>
            </section>

            <section class="legal-section">
                <h2>9. Gangguan di Luar Kendali GameNexa</h2>
                <p>
                    GameNexa berupaya menjaga layanan tetap tersedia, tetapi tidak menjamin layanan selalu
                    bebas dari gangguan. Keterlambatan atau kegagalan dapat disebabkan oleh pemeliharaan,
                    gangguan jaringan, perubahan kebijakan, pembatasan, atau kendala teknis dari game
                    developer, provider, bank, dan penyedia pembayaran.
                </p>
            </section>

            <section class="legal-section">
                <h2>10. Keamanan dan Privasi</h2>
                <p>
                    GameNexa menggunakan langkah yang wajar untuk menjaga keamanan sistem dan informasi
                    transaksi. Data pengguna hanya digunakan untuk keperluan pemrosesan pesanan, dukungan,
                    pencegahan penyalahgunaan, dan kewajiban hukum yang berlaku. Untuk informasi lebih
                    lengkap, silakan baca <a href="{{ route('privacy') }}">Kebijakan Privasi</a>.
                </p>
            </section>

            <section class="legal-section">
                <h2>11. Perubahan Ketentuan</h2>
                <p>
                    GameNexa dapat memperbarui layanan, harga, fitur, dan ketentuan ini apabila diperlukan.
                    Versi terbaru akan ditampilkan pada halaman ini. Penggunaan layanan setelah perubahan
                    dipublikasikan berarti pengguna menerima ketentuan yang telah diperbarui.
                </p>
            </section>

            <section class="legal-section">
                <h2>12. Pertanyaan dan Bantuan</h2>
                <p>
                    Untuk pertanyaan mengenai pesanan atau permintaan pemeriksaan transaksi, hubungi
                    GameNexa melalui WhatsApp
                    <a href="https://wa.link/5h4u8y" target="_blank" rel="noopener noreferrer">wa.link/5h4u8y</a>
                    atau email
                    <a href="mailto:nexadigitalinteractive@gmail.com">nexadigitalinteractive@gmail.com</a>.
                    Sertakan nomor pesanan dan bukti pembayaran jika diperlukan.
                </p>
            </section>

            <section class="legal-section">
                <h2>13. Hukum yang Berlaku</h2>
                <p>
                    Ketentuan ini ditafsirkan berdasarkan hukum yang berlaku di wilayah Republik Indonesia.
                </p>
            </section>

            <div class="legal-notice">
                <strong>Informasi Pengelola</strong>
                <p>GameNexa dikelola oleh nexadigitalinteractive.</p>
                <p>Terakhir diperbarui: 16 Oktober 2026.</p>
            </div>
        </article>
    </div>
</div>
@endsection
