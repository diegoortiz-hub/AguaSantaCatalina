@php
    // El bidón es una capa aparte del paisaje. Si el archivo no está, el hero
    // funciona igual: sólo se salta la animación de entrada del envase.
    $rutaBidon = public_path('images/hero/bidon.png');
    $hayBidon  = is_file($rutaBidon);
@endphp

@push('head')
<style>
    .hero {
        position: relative;
        overflow: hidden;
        background: #0A3D7A;
        min-height: 520px;
    }
    @media (min-width: 768px) {
        .hero { min-height: 620px; }
    }

    /* Capa del paisaje. Va en su propio elemento para poder escalarla sin
       arrastrar el texto ni el bidón. Anclada abajo para que la mesa quede
       siempre al pie, sin importar el recorte que haga "cover". */
    .hero-fondo {
        position: absolute;
        inset: 0;
        background-image: url('{{ asset('images/hero/fondo.jpg') }}');
        background-size: cover;
        background-position: center bottom;
        transform: scale(1.06);
        animation: hero-zoom 14s cubic-bezier(.22,.61,.36,1) forwards;
    }
    @keyframes hero-zoom { to { transform: scale(1); } }

    /* Velo hacia la izquierda: el paisaje es muy luminoso y el texto es blanco. */
    .hero-velo {
        position: absolute;
        inset: 0;
        background: linear-gradient(97deg,
            rgba(8,38,78,.94) 0%,
            rgba(8,38,78,.86) 30%,
            rgba(8,38,78,.55) 52%,
            rgba(8,38,78,.12) 70%,
            rgba(8,38,78,0) 84%);
    }
    @media (max-width: 767px) {
        .hero-velo { background: linear-gradient(180deg, rgba(8,38,78,.92) 0%, rgba(8,38,78,.72) 55%, rgba(8,38,78,.45) 100%); }
    }

    /* El envase entra desde abajo y frena: la curva tiene casi toda la
       desaceleración al final, que es lo que da la sensación de "llegar". */
    /* La altura va en CSS y no en clases de utilidad: así no depende de que
       Tailwind haya rastreado este archivo al compilar. */
    .hero-bidon {
        height: 300px;
        width: auto;
        opacity: 0;
        transform: translateY(90px) scale(.965);
        animation: hero-entra 2s cubic-bezier(.16,.84,.26,1) .25s forwards;
        filter: drop-shadow(0 26px 34px rgba(8,38,78,.42));
    }
    @media (min-width: 640px)  { .hero-bidon { height: 380px; } }
    @media (min-width: 768px)  { .hero-bidon { height: 450px; } }
    @media (min-width: 1024px) { .hero-bidon { height: 580px; } }
    @media (min-width: 1280px) { .hero-bidon { height: 620px; } }
    @keyframes hero-entra {
        to { opacity: 1; transform: translateY(0) scale(1); }
    }

    /* El texto llega cuando el envase ya está puesto. */
    .hero-aparece {
        opacity: 0;
        transform: translateY(18px);
        animation: hero-sube .85s cubic-bezier(.22,.61,.36,1) forwards;
    }
    @keyframes hero-sube { to { opacity: 1; transform: translateY(0); } }

    .hero-d1 { animation-delay: 1.85s; }
    .hero-d2 { animation-delay: 2.15s; }
    .hero-d3 { animation-delay: 2.45s; }
    .hero-d4 { animation-delay: 2.7s; }

    /* Quien pidió menos movimiento ve la escena ya armada. */
    @media (prefers-reduced-motion: reduce) {
        .hero-fondo, .hero-bidon, .hero-aparece {
            animation: none;
            opacity: 1;
            transform: none;
        }
    }
</style>
@endpush

<section class="hero">
    <div class="hero-fondo"></div>
    <div class="hero-velo"></div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 pt-14 pb-10 md:pt-20 md:pb-0">
        <div class="grid md:grid-cols-2 gap-8 md:gap-6 items-end">

            {{-- Columna de texto --}}
            <div class="md:pb-24">
                <span class="hero-aparece hero-d1 inline-flex items-center gap-2 bg-white/15 backdrop-blur-sm text-white text-xs font-semibold px-3 py-1.5 rounded-full mb-6">
                    <span class="w-1.5 h-1.5 bg-[#25D366] rounded-full"></span>
                    Agua certificada · Entrega en 24h
                </span>

                <h1 class="hero-aparece hero-d2 text-4xl md:text-5xl lg:text-6xl font-black text-white leading-[1.05] mb-6" style="font-family:'Poppins',sans-serif;">
                    AGUA PURA<br>
                    <span style="color:#7dd3fc;">PARA TU HOGAR</span><br>
                    Y EMPRESA
                </h1>

                <p class="hero-aparece hero-d3 text-white/85 text-lg leading-relaxed mb-8 max-w-md">
                    Agua purificada por osmosis inversa, con entrega a domicilio en Santiago.
                    Calidad certificada para tu familia y negocio.
                </p>

                <div class="hero-aparece hero-d3 flex flex-wrap gap-3">
                    <a href="{{ route('productos.index') }}" class="btn-primary text-base px-8 py-4">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        Comprar Ahora
                    </a>
                    <a href="{{ route('productos.index') }}" class="inline-flex items-center gap-2 px-8 py-4 text-base font-semibold text-white border-2 border-white/50 rounded-lg hover:border-white hover:bg-white/10 transition">
                        Ver Productos
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>

                <div class="hero-aparece hero-d4 flex gap-8 mt-10">
                    @foreach([['15+','Años de experiencia'],['5k+','Clientes felices'],['100%','Agua certificada']] as [$n,$l])
                    <div>
                        <div class="text-2xl font-black text-white" style="font-family:'Poppins',sans-serif;">{{ $n }}</div>
                        <div class="text-xs text-white/65">{{ $l }}</div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Columna del envase --}}
            @if($hayBidon)
            <div class="flex justify-center md:justify-end items-end">
                <img src="{{ asset('images/hero/bidon.png') }}"
                     alt="Bidón de 20 litros de agua purificada Aguas Santa Catalina"
                     width="398" height="760" fetchpriority="high"
                     class="hero-bidon w-auto">
            </div>
            @endif
        </div>
    </div>
</section>
