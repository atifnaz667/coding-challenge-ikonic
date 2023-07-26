@foreach ($requests as $user)
    <div class="my-2 shadow text-white bg-dark p-1" id="">
        <div class="d-flex justify-content-between">
            <table class="ms-1">
                @if ($mode == 'sent')
                    <td class="align-middle">{{ $user->requestee->name }}</td>
                    <td class="align-middle"> - </td>
                    <td class="align-middle">{{ $user->requestee->email }}</td>
                    <td class="align-middle">
                    @else
                    <td class="align-middle">{{ $user->requestor->name }}</td>
                    <td class="align-middle"> - </td>
                    <td class="align-middle">{{ $user->requestor->email }}</td>
                    <td class="align-middle">
                @endif

            </table>
            <div>
                @if ($mode == 'sent')
                    <button id="cancel_request_btn_" class="btn btn-danger me-1 cancel-request-btn"
                        data-user-id="{{ $user->id }}">Withdraw Request</button>
                @else
                    <button id="accept_request_btn_" class="btn btn-primary me-1 accept-request-btn"
                        data-user-id="{{ $user->id }}">Accept</button>
                @endif
            </div>
        </div>
    </div>
@endforeach
@if ($mode == 'sent')
    <input type="hidden" id="total_sent_request" value="{{ $totalRequests }}">
@else
    <input type="hidden" id="total_received_request" value="{{ $totalRequests }}">
@endif
