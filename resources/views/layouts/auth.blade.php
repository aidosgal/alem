@extends('layouts.app')

@section('content')
<div class="flex min-h-screen">
    <!-- Sidebar -->
    <div class="flex flex-col justify-between w-1/2 px-10 py-16 text-white bg-gradient-to-br from-[#319885] to-[#2a8070]">
        <div>
            <div class="text-4xl font-bold tracking-tight">Alem</div>
        </div>
        
        <div class="max-w-lg">
            <h1 class="text-5xl font-bold leading-tight mb-5">Соединяем работников СНГ с европейскими возможностями</h1>
            <p class="text-lg opacity-90 leading-relaxed">Платформа для менеджеров для создания и управления вакансиями для работников из стран СНГ, ищущих работу в Европе.</p>
            
            <div class="mt-10 space-y-4">
                <div class="flex items-center">
                    <div class="flex items-center justify-center w-6 h-6 mr-3 text-sm bg-white/20 rounded-md">✓</div>
                    <span>Публикуйте вакансии по всей Европе</span>
                </div>
                <div class="flex items-center">
                    <div class="flex items-center justify-center w-6 h-6 mr-3 text-sm bg-white/20 rounded-md">✓</div>
                    <span>Управляйте несколькими организациями</span>
                </div>
                <div class="flex items-center">
                    <div class="flex items-center justify-center w-6 h-6 mr-3 text-sm bg-white/20 rounded-md">✓</div>
                    <span>Находите квалифицированных кандидатов</span>
                </div>
                <div class="flex items-center">
                    <div class="flex items-center justify-center w-6 h-6 mr-3 text-sm bg-white/20 rounded-md">✓</div>
                    <span>Упрощенный процесс найма</span>
                </div>
            </div>
        </div>

        <div class="text-sm opacity-70">
            © 2025 Alem. Все права защищены.
        </div>
    </div>

    <!-- Main Content -->
    <div class="flex items-center justify-center w-1/2 px-10 py-10 bg-white">
        <div class="w-full max-w-md">
            @yield('form')
        </div>
    </div>
</div>
@endsection
