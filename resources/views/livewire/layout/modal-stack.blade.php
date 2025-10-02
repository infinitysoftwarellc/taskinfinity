<div>
    @foreach ($modals as $modal)
        @php
            $parameters = array_merge($modal['parameters'] ?? [], ['modalKey' => $modal['key']]);
        @endphp
        @livewire($modal['component'], $parameters, key('modal-stack-' . $modal['key']))
    @endforeach
</div>
