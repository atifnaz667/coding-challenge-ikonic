@foreach ($suggestedUsers as $user)
    <div class="my-2 shadow  text-white bg-dark p-1" id="">
        <div class="d-flex justify-content-between">
            <table class="ms-1">
                <td class="align-middle">{{ $user->name }}</td>
                <td class="align-middle"> - </td>
                <td class="align-middle">{{ $user->email }}</td>
                <td class="align-middle">
            </table>
            <div id="ajaxContainer">
                <button id="create_request_btn_" data-user-id="{{ $user->id }}"
                    class="btn btn-primary me-1 create-request-btn">Connect</button>
            </div>
        </div>
    </div>
@endforeach
<input type="hidden" id="total_suggestion" value="{{ $totalSuggestions }}">
