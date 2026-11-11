@extends('layouts.app')

@section('title', 'Kebijakan Privasi - GameNexa')

@section('content')
<div class="container" style="padding: 60px 20px; max-width: 900px; margin: 0 auto;">
    <div style="background: rgba(255, 255, 255, 0.02); border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 16px; padding: 40px; backdrop-filter: blur(10px);">
        <h1 style="font-family: var(--font-heading); font-size: 32px; font-weight: 700; color: var(--primary-light); margin-bottom: 10px; text-align: center;">
            Kebijakan Privasi
        </h1>
        <p style="text-align: center; color: var(--text-dim); margin-bottom: 40px;">
            Terakhir diperbarui: {{ date('d F Y') }}
        </p>

        <div style="color: var(--text-muted); line-height: 1.8; font-size: 15px;">
            <section style="margin-bottom: 30px;">
                <h2 style="font-size: 20px; font-weight: 600; color: var(--text-primary); margin-bottom: 15px;">1. Pendahuluan</h2>
                <p style="margin-bottom: 12px;">
                    GameNexa berkomitmen untuk melindungi privasi dan keamanan data pribadi Anda. Kebijakan Privasi ini menjelaskan bagaimana kami mengumpulkan, menggunakan, menyimpan, dan melindungi informasi yang Anda berikan saat menggunakan layanan kami.
                </p>
                <p>
                    Dengan menggunakan layanan GameNexa, Anda menyetujui pengumpulan dan penggunaan informasi sesuai dengan kebijakan ini.
                </p>
            </section>

            <section style="margin-bottom: 30px;">
                <h2 style="font-size: 20px; font-weight: 600; color: var(--text-primary); margin-bottom: 15px;">2. Informasi yang Kami Kumpulkan</h2>
                <p style="margin-bottom: 12px;"><strong>a. Informasi yang Anda Berikan Secara Langsung:</strong></p>
                <ul style="margin-left: 25px; margin-bottom: 15px;">
                    <li>Email untuk konfirmasi pesanan dan invoice</li>
                    <li>Nomor WhatsApp untuk notifikasi (opsional)</li>
                    <li>ID game/akun untuk proses top-up</li>
                    <li>Informasi pembayaran (diproses melalui Doit.id - tidak disimpan di server kami)</li>
                </ul>

                <p style="margin-bottom: 12px;"><strong>b. Informasi yang Dikumpulkan Secara Otomatis:</strong></p>
                <ul style="margin-left: 25px;">
                    <li>Alamat IP dan lokasi geografis</li>
                    <li>Jenis perangkat dan browser yang digunakan</li>
                    <li>Halaman yang dikunjungi dan waktu akses</li>
                    <li>Data cookies untuk meningkatkan pengalaman pengguna</li>
                </ul>
            </section>

            <section style="margin-bottom: 30px;">
                <h2 style="font-size: 20px; font-weight: 600; color: var(--text-primary); margin-bottom: 15px;">3. Cara Kami Menggunakan Informasi</h2>
                <p style="margin-bottom: 12px;">Informasi yang dikumpulkan digunakan untuk:</p>
                <ul style="margin-left: 25px;">
                    <li>Memproses transaksi top-up dan pembayaran</li>
                    <li>Mengirim konfirmasi pesanan dan invoice</li>
                    <li>Memberikan dukungan pelanggan</li>
                    <li>Meningkatkan kualitas layanan dan website</li>
                    <li>Mencegah fraud dan aktivitas mencurigakan</li>
                    <li>Mematuhi kewajiban hukum dan regulasi</li>
                </ul>
            </section>

            <section style="margin-bottom: 30px;">
                <h2 style="font-size: 20px; font-weight: 600; color: var(--text-primary); margin-bottom: 15px;">4. Berbagi Informasi dengan Pihak Ketiga</h2>
                <p style="margin-bottom: 12px;">Kami TIDAK menjual atau menyewakan data pribadi Anda. Namun, kami dapat membagikan informasi dengan:</p>
                <ul style="margin-left: 25px;">
                    <li><strong>Provider Pembayaran (Doit.id):</strong> Untuk memproses transaksi pembayaran</li>
                    <li><strong>Provider Top-up (Digiflazz):</strong> Untuk mengirim produk digital ke akun game Anda</li>
                    <li><strong>Pihak Berwenang:</strong> Jika diwajibkan oleh hukum atau untuk mencegah aktivitas ilegal</li>
                </ul>
                <p style="margin-top: 12px;">
                    Semua pihak ketiga yang kami gunakan telah terikat dengan perjanjian kerahasiaan dan keamanan data.
                </p>
            </section>

            <section style="margin-bottom: 30px;">
                <h2 style="font-size: 20px; font-weight: 600; color: var(--text-primary); margin-bottom: 15px;">5. Keamanan Data</h2>
                <p style="margin-bottom: 12px;">
                    Kami menggunakan berbagai langkah keamanan untuk melindungi informasi pribadi Anda:
                </p>
                <ul style="margin-left: 25px;">
                    <li><strong>Enkripsi SSL/TLS:</strong> Semua komunikasi data dienkripsi</li>
                    <li><strong>Firewall dan Monitoring:</strong> Server dilindungi dengan sistem keamanan berlapis</li>
                    <li><strong>Akses Terbatas:</strong> Hanya personel yang berwenang yang dapat mengakses data</li>
                    <li><strong>Payment Gateway Terpercaya:</strong> Pembayaran diproses melalui Doit.id</li>
                </ul>
                <p style="margin-top: 12px;">
                    Meskipun kami berupaya melindungi data Anda, tidak ada metode transmisi internet yang 100% aman. Anda juga bertanggung jawab menjaga kerahasiaan informasi akun Anda.
                </p>
            </section>

            <section style="margin-bottom: 30px;">
                <h2 style="font-size: 20px; font-weight: 600; color: var(--text-primary); margin-bottom: 15px;">6. Cookies dan Teknologi Pelacakan</h2>
                <p style="margin-bottom: 12px;">
                    Kami menggunakan cookies untuk meningkatkan pengalaman Anda. Cookies adalah file kecil yang disimpan di perangkat Anda yang membantu kami:
                </p>
                <ul style="margin-left: 25px;">
                    <li>Mengingat preferensi dan pengaturan Anda</li>
                    <li>Menganalisis trafik dan perilaku pengguna</li>
                    <li>Menampilkan konten yang relevan</li>
                </ul>
                <p style="margin-top: 12px;">
                    Anda dapat menonaktifkan cookies melalui pengaturan browser, namun beberapa fitur website mungkin tidak berfungsi optimal.
                </p>
            </section>

            <section style="margin-bottom: 30px;">
                <h2 style="font-size: 20px; font-weight: 600; color: var(--text-primary); margin-bottom: 15px;">7. Penyimpanan dan Retensi Data</h2>
                <p style="margin-bottom: 12px;">
                    Data transaksi Anda akan disimpan selama diperlukan untuk:
                </p>
                <ul style="margin-left: 25px;">
                    <li>Memenuhi kewajiban hukum dan perpajakan</li>
                    <li>Menyelesaikan sengketa atau klaim</li>
                    <li>Mencegah fraud dan melindungi hak kami</li>
                </ul>
                <p style="margin-top: 12px;">
                    Data transaksi minimal disimpan selama 5 tahun sesuai regulasi perpajakan Indonesia.
                </p>
            </section>

            <section style="margin-bottom: 30px;">
                <h2 style="font-size: 20px; font-weight: 600; color: var(--text-primary); margin-bottom: 15px;">8. Hak Anda atas Data Pribadi</h2>
                <p style="margin-bottom: 12px;">Anda memiliki hak untuk:</p>
                <ul style="margin-left: 25px;">
                    <li><strong>Akses:</strong> Meminta salinan data pribadi yang kami simpan</li>
                    <li><strong>Koreksi:</strong> Memperbarui atau memperbaiki data yang tidak akurat</li>
                    <li><strong>Penghapusan:</strong> Meminta penghapusan data (dengan batasan tertentu sesuai hukum)</li>
                    <li><strong>Portabilitas:</strong> Mendapatkan data dalam format yang dapat dipindahkan</li>
                    <li><strong>Keberatan:</strong> Menolak pemrosesan data untuk tujuan tertentu</li>
                </ul>
                <p style="margin-top: 12px;">
                    Untuk mengajukan permintaan terkait data pribadi, hubungi kami di support@gamenexa.com.
                </p>
            </section>



            <section style="margin-bottom: 30px;">
                <h2 style="font-size: 20px; font-weight: 600; color: var(--text-primary); margin-bottom: 15px;">9. Privasi Anak-anak</h2>
                <p>
                    Layanan kami tidak ditujukan untuk anak-anak di bawah 13 tahun. Kami tidak secara sengaja mengumpulkan informasi pribadi dari anak-anak. Jika Anda adalah orang tua dan mengetahui anak Anda memberikan informasi kepada kami, hubungi kami untuk menghapus data tersebut.
                </p>
            </section>

            <section style="margin-bottom: 30px;">
                <h2 style="font-size: 20px; font-weight: 600; color: var(--text-primary); margin-bottom: 15px;">10. Perubahan Kebijakan Privasi</h2>
                <p>
                    Kami dapat memperbarui Kebijakan Privasi ini sewaktu-waktu untuk mencerminkan perubahan praktik kami atau karena alasan operasional, hukum, atau regulasi. Perubahan akan diberitahukan melalui website dengan tanggal pembaruan yang baru. Penggunaan berkelanjutan layanan kami setelah perubahan berarti Anda menerima kebijakan yang diperbarui.
                </p>
            </section>

            <section style="margin-bottom: 30px;">
                <h2 style="font-size: 20px; font-weight: 600; color: var(--text-primary); margin-bottom: 15px;">11. Hubungi Kami</h2>
                <p style="margin-bottom: 12px;">
                    Jika Anda memiliki pertanyaan tentang Kebijakan Privasi ini atau ingin mengajukan permintaan terkait data pribadi, hubungi kami melalui:
                </p>
                <ul style="margin-left: 25px;">
                    <li><strong>Email:</strong> support@gamenexa.com</li>
                    <li><strong>WhatsApp:</strong> Support 24/7 (link tersedia di footer)</li>
                </ul>
            </section>

            <div style="margin-top: 40px; padding: 25px; background: rgba(139, 92, 246, 0.1); border: 1px solid rgba(139, 92, 246, 0.3); border-radius: 12px;">
                <p style="margin: 0; color: var(--text-primary); font-weight: 500;">
                    <i class="fa-solid fa-shield-halved" style="color: var(--primary); margin-right: 8px;"></i>
                    Privasi dan keamanan data Anda adalah prioritas kami. Kami berkomitmen untuk melindungi informasi pribadi Anda sesuai dengan standar keamanan tertinggi.
                </p>
            </div>
        </div>

        <div style="margin-top: 40px; text-align: center;">
            <a href="{{ route('home') }}" class="btn-primary" style="display: inline-block; padding: 12px 32px; background: linear-gradient(135deg, var(--primary), var(--secondary)); color: white; text-decoration: none; border-radius: 10px; font-weight: 600; transition: all 0.3s;">
                <i class="fa-solid fa-arrow-left" style="margin-right: 8px;"></i>
                Kembali ke Beranda
            </a>
        </div>
    </div>
</div>
@endsection
