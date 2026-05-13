{{-- Paginador profesional con selector - siempre visible --}}
<div class="pag-wrap">

    {{-- Izquierda: info + selector --}}
    <div class="pag-left">
        @if($paginator->total() > 0)
        <span class="pag-info">
            Mostrando
            <strong>{{ $paginator->firstItem() }}</strong>–<strong>{{ $paginator->lastItem() }}</strong>
            de <strong>{{ $paginator->total() }}</strong>
        </span>
        @else
        <span class="pag-info">Sin registros</span>
        @endif

        <div class="pag-per-page">
            <span>Ver</span>
            <select onchange="cambiarPorPagina(this.value)" class="pag-select">
                @foreach([10, 15, 20, 50] as $size)
                    <option value="{{ $size }}" {{ (int)request('per_page', 15) === $size ? 'selected' : '' }}>
                        {{ $size }}
                    </option>
                @endforeach
            </select>
            <span>por página</span>
        </div>
    </div>

    {{-- Derecha: navegación (solo si hay más de 1 página) --}}
    @if($paginator->hasPages())
    <nav class="pag-nav" aria-label="Paginación">
        @if($paginator->onFirstPage())
            <span class="pag-btn pag-btn--disabled"><i class='bx bx-chevrons-left'></i></span>
            <span class="pag-btn pag-btn--disabled"><i class='bx bx-chevron-left'></i></span>
        @else
            <a class="pag-btn" href="{{ $paginator->url(1) }}"><i class='bx bx-chevrons-left'></i></a>
            <a class="pag-btn" href="{{ $paginator->previousPageUrl() }}"><i class='bx bx-chevron-left'></i></a>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="pag-btn pag-btn--dots">…</span>
            @endif
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="pag-btn pag-btn--active">{{ $page }}</span>
                    @else
                        <a class="pag-btn" href="{{ $url }}">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if($paginator->hasMorePages())
            <a class="pag-btn" href="{{ $paginator->nextPageUrl() }}"><i class='bx bx-chevron-right'></i></a>
            <a class="pag-btn" href="{{ $paginator->url($paginator->lastPage()) }}"><i class='bx bx-chevrons-right'></i></a>
        @else
            <span class="pag-btn pag-btn--disabled"><i class='bx bx-chevron-right'></i></span>
            <span class="pag-btn pag-btn--disabled"><i class='bx bx-chevrons-right'></i></span>
        @endif
    </nav>
    @endif

</div>

<script>
if (typeof cambiarPorPagina === 'undefined') {
    function cambiarPorPagina(valor) {
        var url = new URL(window.location.href);
        url.searchParams.set('per_page', valor);
        url.searchParams.set('page', 1);
        window.location.href = url.toString();
    }
}
</script>
