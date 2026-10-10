<x-layouts::app.sidebar :title="$title ?? null">
    <main class="container-fluid py-4 px-3 px-md-4">
        {{ $slot }}
    </main>
</x-layouts::app.sidebar>
