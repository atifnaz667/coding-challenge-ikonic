@foreach ($connections as $connection)
    <div class="my-2 shadow text-white bg-dark p-1" id="">
        <div class="d-flex justify-content-between">
            <table class="ms-1">
                <td class="align-middle">{{ $connection->name }}</td>
                <td class="align-middle"> - </td>
                <td class="align-middle">{{ $connection->email }}</td>
                <td class="align-middle">
            </table>
            <div>

                    <button style="width: 220px " @if ($connection->common_connections_count == 0) disabled @endif id="get_connections_in_common_{{ $connection->user_id }}"
                        data-user-id="{{ $connection->user_id }}" class="btn btn-primary" type="button"
                        data-bs-toggle="collapse" data-bs-target="#collapse_{{ $connection->user_id }}"
                        aria-expanded="false" aria-controls="collapseExample">
                        Connections in common ({{ $connection->common_connections_count }})
                    </button>
                <button id="create_request_btn_" class="btn btn-danger me-1 remove-connection-btn" data-user-id="{{ $connection->id }}">Remove Connection</button>
            </div>

        </div>
        <div class="collapse" id="collapse_{{ $connection->user_id }}">

            <div id="content_{{ $connection->user_id }}" class="p-2">
                {{-- Display data here --}}

            </div>
            <div id="connections_in_common_skeletons_{{ $connection->user_id }}" class="d-none">
                @for ($i = 0; $i < 10; $i++)
                    <x-skeleton />
                @endfor
            </div>
            <div class="d-flex justify-content-center w-100 py-2">
                <button class="btn btn-sm btn-primary"
                    id="load_more_connections_in_common_{{ $connection->user_id }}">Load
                    more</button>
            </div>
        </div>
    </div>
@endforeach
<input type="hidden" id="total_connections" value="{{ $totalConnections }}">

<script>
    $(document).ready(function() {
        $('[id^="get_connections_in_common_"]').click(function() {
            var userId = $(this).data('user-id');
            $('#content_' + userId).empty();
            $('#connections_in_common_skeletons_' + userId).removeClass('d-none');
            $.ajax({
                url: "{{ route('common_connection.index') }}",
                type: 'GET',
                data: {
                    user_id: userId
                },
                success: function(response) {
                    $('#connections_in_common_skeletons_' + userId).addClass('d-none');
                    $('#content_' + userId).append(response);
                },
                error: function(xhr, status, error) {
                    console.log(error);
                }
            });
        });
    });
</script>
