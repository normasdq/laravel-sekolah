<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CBT - SMP Tunas Harapan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body {
            user-select: none;
            -webkit-user-select: none;
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 5px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #e5e7eb;
            border-radius: 10px;
        }
    </style>
</head>

<body class="bg-slate-50 font-sans antialiased">

    <!-- Header Fixed -->
    <header class="fixed top-0 inset-x-0 bg-white/80 backdrop-blur-md border-b border-slate-200 z-50">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div
                    class="w-12 h-12 bg-blue-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-blue-200">
                    <i class="fa-solid fa-laptop-code"></i>
                </div>
                <div>
                    <h2 class="font-bold text-slate-900 leading-none">Ujian Seleksi Online</h2>
                    <p class="text-xs text-slate-500 mt-1 uppercase tracking-widest font-bold">
                        {{ auth()->user()->registration->name ?? auth()->user()->name }}</p>
                </div>
            </div>

            <div class="flex items-center gap-6">
                <div class="hidden md:block text-right">
                    <p class="text-[10px] font-black text-slate-400 uppercase">Sisa Waktu</p>
                    <p id="timer" class="font-mono text-2xl font-black text-blue-600">00:00:00</p>
                </div>
                <button onclick="confirmFinish()"
                    class="px-6 py-3 bg-emerald-500 text-white font-bold rounded-xl hover:bg-emerald-600 transition-all text-sm">
                    Selesai Ujian
                </button>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-6 pt-32 pb-12 grid lg:grid-cols-12 gap-8">

        <!-- Left: Question Area -->
        <div class="lg:col-span-8">
            <form id="exam-form" action="{{ route('pendaftar.ujian.submit') }}" method="POST">
                @csrf
                <div class="space-y-6">
                    @foreach ($questions as $index => $q)
                        <div id="q-{{ $index + 1 }}"
                            class="question-card bg-white rounded-[2.5rem] p-8 md:p-10 border border-slate-200 shadow-sm transition-all duration-300 {{ $index == 0 ? 'block' : 'hidden' }}">
                            <div class="flex items-center justify-between mb-8">
                                <span
                                    class="px-4 py-1.5 bg-slate-100 rounded-full text-[10px] font-black text-slate-500 uppercase tracking-widest">Pertanyaan
                                    {{ $index + 1 }} dari {{ count($questions) }}</span>
                                <span class="text-slate-300 text-xs italic">Point: {{ $q->points }}</span>
                            </div>

                            <div class="text-xl text-slate-800 font-medium leading-relaxed mb-10">
                                {{ $q->question_text }}
                            </div>

                            <div class="grid gap-4">
                                @foreach (['A', 'B', 'C', 'D'] as $opt)
                                    @php $optField = 'option_'.strtolower($opt); @endphp
                                    <label
                                        class="group flex items-center gap-5 p-5 rounded-2xl border-2 border-slate-50 hover:border-blue-200 hover:bg-blue-50/50 transition-all cursor-pointer relative overflow-hidden">
                                        <input type="radio" name="answers[{{ $q->id }}]"
                                            value="{{ $opt }}" class="peer hidden"
                                            onchange="markAsDone({{ $index + 1 }})">
                                        <div
                                            class="absolute inset-y-0 left-0 w-1 bg-blue-500 transition-transform -translate-x-full peer-checked:translate-x-0">
                                        </div>
                                        <span
                                            class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center font-black text-sm text-slate-500 group-hover:bg-blue-500 group-hover:text-white transition-all peer-checked:bg-blue-600 peer-checked:text-white shadow-sm">
                                            {{ $opt }}
                                        </span>
                                        <span
                                            class="text-slate-700 font-semibold group-hover:text-blue-900 transition-colors">{{ $q->$optField }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Nav Button -->
                <div class="mt-8 flex items-center justify-between px-2">
                    <button type="button" onclick="prevQ()"
                        class="px-8 py-4 bg-white text-slate-600 font-bold rounded-2xl border border-slate-200 hover:bg-slate-50 transition-all">Sebelumnya</button>
                    <button type="button" onclick="nextQ()"
                        class="px-8 py-4 bg-blue-600 text-white font-bold rounded-2xl shadow-lg shadow-blue-200 hover:bg-blue-700 transition-all">Selanjutnya</button>
                </div>
            </form>
        </div>

        <!-- Right: Navigation Sidebar -->
        <div class="lg:col-span-4">
            <div class="bg-white rounded-[2.5rem] p-8 border border-slate-200 shadow-sm sticky top-32">
                <h4 class="font-bold text-slate-900 mb-6 flex items-center gap-2">
                    <i class="fa-solid fa-layer-group text-blue-500"></i> Navigasi Soal
                </h4>
                <div class="grid grid-cols-5 gap-3 h-[350px] overflow-y-auto pr-2 custom-scrollbar">
                    @foreach ($questions as $index => $q)
                        <button onclick="goToQ({{ $index + 1 }})" id="nav-{{ $index + 1 }}"
                            class="nav-btn w-12 h-12 rounded-xl border-2 border-slate-50 bg-slate-50 text-slate-400 font-black text-xs transition-all hover:border-blue-200">
                            {{ $index + 1 }}
                        </button>
                    @endforeach
                </div>

                <div class="mt-8 pt-6 border-t border-slate-100 grid grid-cols-2 gap-4">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 bg-blue-600 rounded-sm"></div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase">Terjawab</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 bg-slate-200 rounded-sm"></div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase">Belum</span>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        let currentQ = 1;
        const totalQ = {{ count($questions) }};

        function showQ() {
            document.querySelectorAll('.question-card').forEach(c => c.classList.add('hidden'));
            document.getElementById('q-' + currentQ).classList.remove('hidden');

            document.querySelectorAll('.nav-btn').forEach(b => b.classList.remove('ring-2', 'ring-blue-500',
                'ring-offset-2'));
            document.getElementById('nav-' + currentQ).classList.add('ring-2', 'ring-blue-500', 'ring-offset-2');
        }

        function nextQ() {
            if (currentQ < totalQ) {
                currentQ++;
                showQ();
            }
        }

        function prevQ() {
            if (currentQ > 1) {
                currentQ--;
                showQ();
            }
        }

        function goToQ(n) {
            currentQ = n;
            showQ();
        }

        function markAsDone(n) {
            const btn = document.getElementById('nav-' + n);
            btn.classList.remove('bg-slate-50', 'text-slate-400');
            btn.classList.add('bg-blue-600', 'text-white', 'border-blue-600');
        }

        // Timer Logic (Sinkron dengan Waktu Server)
        let duration = {{ max(0, (int) now()->diffInSeconds($endTime, false)) }};
        let isSubmitting = false;

        function submitExam() {
            if (isSubmitting) return;
            isSubmitting = true;
            document.getElementById('exam-form').submit();
        }

        const timerDisplay = document.getElementById('timer');
        const interval = setInterval(() => {
            let h = Math.floor(duration / 3600);
            let m = Math.floor((duration % 3600) / 60);
            let s = duration % 60;
            timerDisplay.innerHTML =
                `${String(h).padStart(2,'0')}:${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`;
            if (duration <= 0) {
                clearInterval(interval);
                submitExam();
            }
            duration--;
        }, 1000);

        function confirmFinish() {
            if (confirm('Apakah Anda yakin ingin mengakhiri ujian?')) {
                submitExam();
            }
        }

        // Proteksi
        document.addEventListener('contextmenu', e => e.preventDefault());
        window.onblur = () => {
            console.warn("Dilarang meninggalkan tab ujian!");
        };
    </script>
</body>

</html>
