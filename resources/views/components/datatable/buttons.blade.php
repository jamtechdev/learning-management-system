@props(['data' => [], 'width' => 100])

<div style="min-width: {{ $width }}px !important;">
    <div class="flex justify-center space-x-2">
        @foreach ($data as $item)
            @php
                $dataAttributes = '';
                if (!empty($item['data'])) {
                    foreach ($item['data'] as $attrKey => $attrValue) {
                        $dataAttributes .= ' data-' . $attrKey . '="' . e($attrValue) . '"';
                    }
                }
                $tag = $item['tag'] ?? 'button';
                $href = $item['href'] ?? 'javascript:void(0);';
                $tableId = $item['data']['table_id'] ?? 'questionlevel-table';
                $url = $item['data']['url'] ?? '';
            @endphp

            @if ($tag === 'a')
                <a href="{{ $href }}" class="{{ $item['class'] }}" title="{{ $item['title'] ?? '' }}" {!! $dataAttributes !!}>
                    <i class="{{ $item['icon'] }}"></i>
                </a>
            @else
                <span
                    x-data="{
                        deleteItem() {
                            if (!confirm('Are you sure?')) return;

                            fetch('{{ $url }}', {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify({ _method: 'DELETE' })
                            }).then(response => {
                                if (response.ok) {
                                    const dt = $('#' + '{{ $tableId }}').DataTable();
                                    if (dt) dt.ajax.reload(null, false);
                                } else {
                                    alert('Failed to delete');
                                }
                            }).catch(() => alert('Request failed'));
                        }
                    }"
                    @click="deleteItem"
                    class="{{ $item['class'] }}"
                    title="{{ $item['title'] ?? '' }}"
                    {!! $dataAttributes !!}
                    role="button"
                >
                    <i class="{{ $item['icon'] }}"></i>
                </span>
            @endif
        @endforeach
    </div>
</div>
