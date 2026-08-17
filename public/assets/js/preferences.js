document.addEventListener('DOMContentLoaded', () => {
    const themeSwitch = document.getElementById('themeSwitch');
    const languageSwitches = document.querySelectorAll('.language-switch');
    const root = document.documentElement;

    const translations = {
        'Beranda': 'Home',
        'Proyek': 'Projects',
        'Tentang': 'About',
        'Layanan': 'Services',
        'Kontak': 'Contact',
        'Games': 'Games',
        'Mulai Proyek': 'Start Project',
        'Digital product studio': 'Digital product studio',
        'Web & aplikasi,': 'Web & apps,',
        'beres lebih cepat.': 'built faster.',
        'Avinto Project membantu ide bisnis, tugas, dan operasional jadi produk digital yang rapi, modern, dan ramah budget.': 'Avinto Project turns business ideas, tasks, and operations into clean, modern, budget-friendly digital products.',
        'Wujudkan ide sekarang': 'Start your idea',
        'Lihat hasil nyata': 'View real work',
        'Proses lebih cepat': 'Faster process',
        'Desain elegan': 'Elegant design',
        'Biaya bersahabat': 'Friendly budget',
        '35+ project delivered': '35+ projects delivered',
        'Built through real work': 'Built through real work',
        'Pengalaman lintas institusi, bisnis, dan produk independen.': 'Experience across institutions, businesses, and independent products.',
        'Proyek diselesaikan': 'Completed projects',
        'Aplikasi institusi': 'Institutional apps',
        'Tahun berkarya': 'Years building',
        'Repositori publik': 'Public repositories',
        'Meet the builder': 'Meet the builder',
        'Teknologi, desain, dan bisnis dalam satu perspektif.': 'Technology, design, and business in one perspective.',
        'Lihat perjalanan saya': 'See my journey',
        'Unduh CV': 'Download CV',
        'Digital footprint': 'Digital footprint',
        'Kode, kreativitas, dan eksperimen AI.': 'Code, creativity, and AI experiments.',
        'Bukan hanya hasil akhir. Ini adalah jejak proses belajar, membangun, dan mengeksplorasi cara baru menciptakan produk digital.': 'More than final results. This is the trail of learning, building, and exploring new ways to create digital products.',
        'Buka GitHub': 'Open GitHub',
        'Open source activity': 'Open source activity',
        '25 repositori publik dan terus bertumbuh.': '25 public repositories and still growing.',
        'AI visual exploration': 'AI visual exploration',
        'Teknologi juga bisa terasa personal.': 'Technology can feel personal too.',
        'Serangkaian visual berbasis AI yang menggambarkan cara saya bekerja: mendengar kebutuhan, berdiskusi, lalu mengubah ide menjadi solusi yang dapat digunakan.': 'AI-generated visuals that reflect how I work: listening, discussing, then turning ideas into usable solutions.',
        'Dari ide sampai siap digunakan.': 'From idea to launch.',
        'Satu partner untuk desain, pengembangan, deployment, dan pengembangan lanjutan.': 'One partner for design, development, deployment, and future growth.',
        'Minta estimasi': 'Request estimate',
        'Selected work': 'Selected work',
        'Karya yang memecahkan masalah nyata.': 'Work that solves real problems.',
        'Beberapa produk untuk pendidikan, operasional institusi, layanan publik, dan bisnis.': 'Products for education, institutional operations, public services, and business.',
        'Lihat seluruh proyek': 'View all projects',
        'Cara bekerja': 'Workflow',
        'Kolaborasi yang rapi dari awal.': 'A clear collaboration from the start.',
        'Setiap produk dimulai dari memahami masalah, bukan langsung menulis kode.': 'Every product starts by understanding the problem before writing code.',
        'Discovery': 'Discovery',
        'Memetakan tujuan, pengguna, ruang lingkup, dan prioritas.': 'Map goals, users, scope, and priorities.',
        'Design & Build': 'Design & Build',
        'Merancang pengalaman lalu membangun secara iteratif.': 'Design the experience, then build iteratively.',
        'Test & Launch': 'Test & Launch',
        'Menguji, menyiapkan deployment, dan memastikan handover jelas.': 'Test, prepare deployment, and deliver a clear handover.',
        'Grow': 'Grow',
        'Maintenance dan pengembangan lanjutan berdasarkan kebutuhan.': 'Maintenance and future improvements as needed.',
        'Leadership': 'Leadership',
        'Teknis yang tetap selaras dengan bisnis.': 'Technical work aligned with business.',
        'Testimoni': 'Testimonials',
        'Dipercaya dalam kolaborasi nyata.': 'Trusted in real collaborations.',
        'Punya ide?': 'Have an idea?',
        'Mari jadikan konsep Anda produk yang bisa digunakan.': 'Let’s turn your concept into a usable product.',
        'Ceritakan kebutuhan, target, dan kisaran budget. Saya akan menghubungi Anda untuk membahas arah terbaiknya.': 'Share your needs, target, and budget range. I will contact you to discuss the best direction.',
        'Mulai proyek sekarang': 'Start a project now',
        'WhatsApp': 'WhatsApp',
        'Portofolio': 'Portfolio',
        'Profil & Pengalaman': 'Profile & Experience',
        'Ajukan Proyek': 'Request Project',
        'Semua Proyek': 'All Projects',
        'Hubungi': 'Contact',
        'Jelajahi': 'Explore',
        'Mitra teknologi untuk mengubah ide menjadi produk digital yang matang, menarik, dan siap digunakan.': 'A technology partner for turning ideas into mature, attractive, ready-to-use digital products.',
        'Build with purpose': 'Build with purpose',
        'Global reach': 'Global reach',
        'Visitor': 'Visitor',
        'Melihat ide Avinto menjangkau lebih banyak tempat.': 'Watching Avinto ideas reach more places.',
        'Designed and engineered by Paris Alvito.': 'Designed and engineered by Paris Alvito.',
        'Masuk ke dashboard': 'Sign in to dashboard',
        'Username': 'Username',
        'Password': 'Password',
        'Masuk sebagai admin': 'Sign in as admin',
        'Buka Vinto AI': 'Open Vinto AI',
        'Tutup chatbot': 'Close chatbot',
        'Asisten Avinto Project': 'Avinto Project assistant',
        'Halo! Saya Vinto. Ceritakan ide atau kebutuhan digital Anda, saya bantu pilih layanan dan langkah awal yang paling sesuai.': 'Hi! I am Vinto. Tell me your digital idea or need, and I will help choose the most suitable service and first step.',
        'Lihat layanan': 'View services',
        'Kisaran biaya': 'Price range',
        'Lihat pengalaman': 'View experience',
        'Vinto adalah asisten AI dan dapat membuat kekeliruan.': 'Vinto is an AI assistant and may make mistakes.',
        'Portfolio archive': 'Portfolio archive',
        'Produk digital yang sudah': 'Digital products already',
        'menjadi bagian dari dunia nyata.': 'working in the real world.',
        'Jelajahi karya lintas web, mobile, sistem informasi, pendidikan, dan produk institusi.': 'Explore work across web, mobile, information systems, education, and institutional products.',
        'Semua': 'All',
        'Paris Alvito / Alvin Alvito': 'Paris Alvito / Alvin Alvito',
        'Engineer yang nyaman berbicara': 'An engineer fluent',
        'dengan kode maupun bisnis.': 'in code and business.',
        'Bekerja bersama saya': 'Work with me',
        'Lihat CV': 'View CV',
        'Core expertise': 'Core expertise',
        'Full-stack dalam arti yang sesungguhnya.': 'Full-stack in the truest sense.',
        'Terbiasa bergerak dari diskusi stakeholder, desain arsitektur, antarmuka, backend, database, integrasi, pengujian, deployment, hingga maintenance.': 'Comfortable moving from stakeholder discussion, architecture, UI, backend, database, integration, testing, deployment, to maintenance.',
        'Professional journey': 'Professional journey',
        'Bertumbuh lewat tanggung jawab yang nyata.': 'Growing through real responsibility.',
        'Dari laboratorium kampus hingga memimpin divisi IT, setiap peran membentuk cara saya membangun teknologi: terukur, komunikatif, dan bertanggung jawab sampai produk digunakan.': 'From campus labs to leading an IT division, every role shaped how I build technology: measured, communicative, and responsible until the product is used.',
        'Tahun berkarya': 'Years building',
        'Proyek selesai': 'Completed projects',
        'Peran leadership': 'Leadership roles',
        'Current role': 'Current role',
        'Pendidikan': 'Education',
        'Fondasi akademik.': 'Academic foundation.',
        'Professional strengths': 'Professional strengths',
        'Lebih dari kemampuan teknis.': 'More than technical skills.',
        'Kepemimpinan tim': 'Team leadership',
        'Stakeholder communication': 'Stakeholder communication',
        'Problem solving': 'Problem solving',
        'Kolaborasi lintas fungsi': 'Cross-functional collaboration',
        'Adaptasi cepat': 'Fast adaptation',
        'Product ownership': 'Product ownership',
        'Next collaboration': 'Next collaboration',
        'Ada tantangan digital yang perlu diselesaikan?': 'Have a digital challenge to solve?',
        'Mari diskusikan konteksnya dan cari pendekatan yang realistis.': 'Let’s discuss the context and find a realistic approach.',
        'Ceritakan kebutuhan Anda': 'Tell me your needs',
        'Project inquiry': 'Project inquiry',
        'Ceritakan apa yang ingin': 'Tell me what',
        'Anda bangun.': 'you want to build.',
        'Semakin jelas konteksnya, semakin tepat arah awal yang bisa kami rekomendasikan.': 'The clearer the context, the better the first recommendation.',
        'Sebelum mengirim': 'Before sending',
        'Yang terjadi selanjutnya.': 'What happens next.',
        'Review kebutuhan': 'Needs review',
        'Detail Anda akan dipelajari untuk memahami ruang lingkup awal.': 'Your details will be reviewed to understand the initial scope.',
        'Diskusi singkat': 'Short discussion',
        'Kami menghubungi Anda melalui kanal pilihan untuk melengkapi konteks.': 'We contact you through your chosen channel to complete the context.',
        'Estimasi & arah kerja': 'Estimate & direction',
        'Anda menerima rekomendasi solusi, estimasi biaya, dan langkah pengerjaan.': 'You receive solution recommendations, cost estimates, and work steps.',
        'Lebih nyaman bicara langsung?': 'Prefer talking directly?',
        'Nama lengkap *': 'Full name *',
        'Nomor WhatsApp / HP *': 'WhatsApp / phone number *',
        'Email': 'Email',
        'Perusahaan / organisasi': 'Company / organization',
        'Jenis proyek *': 'Project type *',
        'Pilih jenis proyek': 'Choose project type',
        'Kisaran budget *': 'Budget range *',
        'Pilih kisaran': 'Choose range',
        'Detail kebutuhan *': 'Project details *',
        'Target selesai': 'Target deadline',
        'Preferensi dihubungi *': 'Preferred contact *',
        'Kirim permintaan proyek': 'Send project request',
        'Data hanya digunakan untuk menindaklanjuti permintaan Anda.': 'Data is only used to follow up on your request.',
    };

    const placeholders = {
        'Tanyakan tentang layanan atau proyek...': 'Ask about services or projects...',
        'Nama Anda': 'Your name',
        '08xxxxxxxxxx': '08xxxxxxxxxx',
        'nama@email.com': 'name@email.com',
        'Opsional': 'Optional',
        'Ceritakan tujuan proyek, pengguna, fitur utama, masalah yang ingin diselesaikan, dan referensi bila ada...': 'Tell us the project goal, users, main features, problem to solve, and references if any...',
    };

    const translateText = (node, language) => {
        if (!node.__avintoOriginalText) node.__avintoOriginalText = node.nodeValue;
        if (language === 'id') {
            node.nodeValue = node.__avintoOriginalText;
            return;
        }

        const original = node.__avintoOriginalText;
        const trimmed = original.trim();
        if (!trimmed || !translations[trimmed]) return;
        node.nodeValue = original.replace(trimmed, translations[trimmed]);
    };

    const applyLanguage = (language) => {
        root.dataset.language = language;
        root.lang = language;
        localStorage.setItem('avinto-language', language);
        languageSwitches.forEach(button => button.setAttribute('aria-pressed', String(language === 'en')));

        const walker = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT, {
            acceptNode(node) {
                const parent = node.parentElement;
                if (!parent || ['SCRIPT', 'STYLE', 'TEXTAREA'].includes(parent.tagName)) return NodeFilter.FILTER_REJECT;
                return node.nodeValue.trim() ? NodeFilter.FILTER_ACCEPT : NodeFilter.FILTER_REJECT;
            }
        });

        const nodes = [];
        while (walker.nextNode()) nodes.push(walker.currentNode);
        nodes.forEach(node => translateText(node, language));

        document.querySelectorAll('[placeholder]').forEach(element => {
            if (!element.dataset.avintoOriginalPlaceholder) element.dataset.avintoOriginalPlaceholder = element.getAttribute('placeholder');
            const original = element.dataset.avintoOriginalPlaceholder;
            element.setAttribute('placeholder', language === 'en' ? (placeholders[original] || original) : original);
        });
    };

    const applyTheme = (theme) => {
        root.dataset.theme = theme;
        localStorage.setItem('avinto-theme', theme);
        themeSwitch?.setAttribute('aria-pressed', String(theme === 'dark'));
        themeSwitch?.setAttribute('aria-label', theme === 'dark' ? 'Aktifkan light mode' : 'Aktifkan dark mode');
    };

    themeSwitch?.addEventListener('click', () => applyTheme(root.dataset.theme === 'dark' ? 'light' : 'dark'));
    languageSwitches.forEach(button => button.addEventListener('click', () => applyLanguage(root.dataset.language === 'en' ? 'id' : 'en')));

    applyTheme(localStorage.getItem('avinto-theme') || root.dataset.theme || 'dark');
    applyLanguage(localStorage.getItem('avinto-language') || root.dataset.language || 'id');
});
