<div class="row justify-content-center mt-5">
    <div class="col-12">
        <div class="card shadow text-white bg-dark">
            <div class="card-header">Coding Challenge - Network connections</div>
            <div class="card-body">
                <div class="btn-group w-100 mb-3" role="group" aria-label="Basic radio toggle button group">
                    <input type="radio" class="btn-check" name="btnradio" id="btnradio1" autocomplete="off" checked>
                    <label class="btn btn-outline-primary" for="btnradio1" id="get_suggestions_btn">Suggestions
                        (<span id="suggestedUsersCount"></span>)</label>

                    <input type="radio" class="btn-check" name="btnradio" id="btnradio2" autocomplete="off">
                    <label class="btn btn-outline-primary" for="btnradio2" id="get_sent_requests_btn">Sent Requests
                        (<span id="sentRequestsCount"></span>)</label>

                    <input type="radio" class="btn-check" name="btnradio" id="btnradio3" autocomplete="off">
                    <label class="btn btn-outline-primary" for="btnradio3" id="get_received_requests_btn">Received
                        Requests(<span id="receivedRequestsCount"></span>)</label>

                    <input type="radio" class="btn-check" name="btnradio" id="btnradio4" autocomplete="off">
                    <label class="btn btn-outline-primary" for="btnradio4" id="get_connections_btn">Connections
                        (<span id="connectionsCount"></span>)</label>
                </div>
                <hr>

                <div id="records" class="d-none">
                </div>


                <div id="skeleton" class="d-none">
                    @for ($i = 0; $i < 10; $i++)
                        <x-skeleton />
                    @endfor
                </div>
                <div class="d-flex justify-content-center mt-2 py-3" id="suggestion_btn_parent">
                    <button class="btn btn-primary" id="load_suggestion_btn">Load more</button>
                </div>
                <div class="d-flex justify-content-center mt-2 py-3 d-none" id="sent_request_btn_parent">
                    <button class="btn btn-primary" id="load_sent_request_btn">Load more</button>
                </div>
                <div class="d-flex justify-content-center mt-2 py-3 d-none" id="received_request_btn_parent">
                    <button class="btn btn-primary" id="load_received_request_btn">Load more</button>
                </div>
                <div class="d-flex justify-content-center mt-2 py-3 d-none" id="connection_btn_parent">
                    <button class="btn btn-primary" id="load_connection_btn">Load more</button>
                </div>
            </div>
        </div>
    </div>

</div>

<div id="connections_in_common_skeleton" class="d-none">
    <br>
    <span class="fw-bold text-white">Loading Skeletons</span>
    <div class="px-2">
        @for ($i = 0; $i < 10; $i++)
            <x-skeleton />
        @endfor
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        var currentSuggestionPage = 1;
        var currentReceivedRequestPage = 1;
        var currentSentRequestPage = 1;
        var currentConnectionPage = 1;
        var totalSuggestions = 0;
        var totalReceivedRequests = 0;
        var totalSentRequests = 0;
        var totalConnections = 0;
        getCounts();

        document.getElementById('records').addEventListener('click', function(event) {
            if (event.target && event.target.matches('.create-request-btn')) {
                var userId = event.target.getAttribute('data-user-id');
                sendRequest(userId);
            } else if (event.target && event.target.matches('.cancel-request-btn')) {
                var userId = event.target.getAttribute('data-user-id');
                cancelRequest(userId);
            } else if (event.target && event.target.matches('.accept-request-btn')) {
                var userId = event.target.getAttribute('data-user-id');
                acceptRequest(userId);
            }else if (event.target && event.target.matches('.remove-connection-btn')) {
                var userId = event.target.getAttribute('data-user-id');
                removeConnection(userId);
            }
        });

        function sendRequest(userId) {
            var _token = $('input[name="_token"]').val();
            $.ajax({
                url: "{{ route('connection.store') }}",
                method: 'POST',
                data: {
                    _token: _token,
                    userId: userId,
                },
                success: function(response) {
                    $('#get_suggestions_btn').trigger('click');
                    getCounts();
                },
                error: function(error) {
                    console.log(error);
                }
            });
        }

        function cancelRequest(userId) {
            var _token = $('input[name="_token"]').val();
            $.ajax({
                url: "{{ route('send_request.destroy', '') }}" + "/" + userId,
                method: 'DELETE',
                data: {
                    _token: _token,
                },
                success: function(response) {
                    $('#get_sent_requests_btn').trigger('click');
                    getCounts();
                },
                error: function(error) {
                    console.log(error);
                }
            });
        }

        function acceptRequest(userId) {
            var _token = $('input[name="_token"]').val();
            $.ajax({
                url: "{{ route('recived_request.update', '') }}" + "/" + userId,
                method: 'PUT',
                data: {
                    userId: userId,
                    _token: _token,
                },
                success: function(response) {
                    $('#get_received_requests_btn').trigger('click');
                    getCounts();
                },
                error: function(error) {
                    console.log(error);
                }
            });
        }

        function removeConnection(userId) {
            var _token = $('input[name="_token"]').val();
            $.ajax({
                url: "{{ route('connection.destroy', '') }}" + "/" + userId,
                method: 'DELETE',
                data: {
                    _token: _token,
                },
                success: function(response) {
                    $('#get_connections_btn').trigger('click');
                    getCounts();
                },
                error: function(error) {
                    console.log(error);
                }
            });
        }

        function getSuggestions(page) {
            $.ajax({
                url: "{{ route('suggestion.index') }}",
                method: 'GET',
                data: {
                    page: page
                },
                success: function(response) {
                    $('#skeleton').addClass('d-none');
                    $('#records').append(response);
                    var totalSuggestions = document.getElementById("total_suggestion").value;
                    currentSuggestionPage = page;
                    if (currentSuggestionPage * 10 >= totalSuggestions) {
                        $('#suggestion_btn_parent').addClass('d-none');
                    }
                },
                error: function(error) {
                    console.log(error);
                }
            });
        }

        function getRequests(page, mode) {
            if (mode == 'recived')
                var route = "{{ route('recived_request.index') }}";
            else
                var route = "{{ route('send_request.index') }}";

            $.ajax({
                url: route,
                method: 'GET',
                data: {
                    mode: mode,
                    page: page
                },
                success: function(response) {
                    $('#skeleton').addClass('d-none');
                    $('#records').append(response);
                    if (mode == 'recived') {
                        var totalRequests = document.getElementById("total_received_request").value;
                        currentReceivedRequestPage = page;
                        if (currentReceivedRequestPage * 10 >= totalRequests) {
                            $('#received_request_btn_parent').addClass('d-none');
                        }
                    } else {
                        currentSentRequestPage = page;
                        var totalRequests = document.getElementById("total_sent_request").value;
                        if (currentSentRequestPage * 10 >= totalRequests) {
                            $('#sent_request_btn_parent').addClass('d-none');
                        }
                    }
                },
                error: function(error) {
                    console.log(error);
                }
            });
        }

        function getConnections(page) {
            $.ajax({
                url: "{{ route('connection.index') }}",
                method: 'GET',
                data: {
                    page: page
                },
                success: function(response) {
                    $('#skeleton').addClass('d-none');
                    $('#records').append(response);
                    var totalConections = document.getElementById("total_connections").value;
                    currentConnectionPage = page;
                    if (currentConnectionPage * 10 >= totalConections) {
                        $('#connection_btn_parent').addClass('d-none');
                    }
                },
                error: function(error) {
                    console.log(error);
                }
            });
        }

        $(document).on('click', '#load_suggestion_btn', function() {
            var nextPage = currentSuggestionPage + 1;
            $('#skeleton').removeClass('d-none');
            getSuggestions(nextPage);
        });
        $(document).on('click', '#load_received_request_btn', function() {
            var nextPage = currentReceivedRequestPage + 1;
            $('#skeleton').removeClass('d-none');
            var mode = 'recived';
            getRequests(nextPage, mode);
        });
        $(document).on('click', '#load_sent_request_btn', function() {
            var nextPage = currentSentRequestPage + 1;
            $('#skeleton').removeClass('d-none');
            var mode = 'sent';
            getRequests(nextPage, mode);
        });
        $(document).on('click', '#load_connection_btn', function() {
            var nextPage = currentConnectionPage + 1;
            $('#skeleton').removeClass('d-none');
            getConnections(nextPage);
        });

        $('#get_suggestions_btn').on('click', function() {
            $('#records').empty();
            $('#skeleton').removeClass('d-none');
            $('#records').removeClass('d-none');
            $('#sent_request_btn_parent').addClass('d-none');
            $('#received_request_btn_parent').addClass('d-none');
            $('#connection_btn_parent').addClass('d-none');
            $('#suggestion_btn_parent').removeClass('d-none');
            getSuggestions(1);
        });
        $('#get_sent_requests_btn').on('click', function() {
            $('#records').empty();
            $('#skeleton').removeClass('d-none');
            $('#sent_request_btn_parent').removeClass('d-none');
            $('#received_request_btn_parent').addClass('d-none');
            $('#connection_btn_parent').addClass('d-none');
            $('#suggestion_btn_parent').addClass('d-none');
            $('#records').removeClass('d-none');
            var mode = 'sent';
            getRequests(1, mode);
        });
        $('#get_received_requests_btn').on('click', function() {
            $('#records').empty();
            $('#skeleton').removeClass('d-none');
            $('#sent_request_btn_parent').addClass('d-none');
            $('#received_request_btn_parent').removeClass('d-none');
            $('#connection_btn_parent').addClass('d-none');
            $('#suggestion_btn_parent').addClass('d-none');
            $('#records').removeClass('d-none');
            var mode = 'recived';
            getRequests(1, mode);
        });
        $('#get_connections_btn').on('click', function() {
            $('#records').empty();
            $('#skeleton').removeClass('d-none');
            $('#sent_request_btn_parent').addClass('d-none');
            $('#received_request_btn_parent').addClass('d-none');
            $('#connection_btn_parent').removeClass('d-none');
            $('#suggestion_btn_parent').addClass('d-none');
            $('#records').removeClass('d-none');
            getConnections(1);
        });

        $('#get_suggestions_btn').trigger('click');

        function getCounts(){
            $.ajax({
                url: "{{ route('getCounts') }}",
                method: 'GET',
                success: function(response) {
                    $('#suggestedUsersCount').html(response.countsArray.suggestedUsersCount);
                    $('#sentRequestsCount').html(response.countsArray.sentRequestsCount);
                    $('#receivedRequestsCount').html(response.countsArray.receivedRequestsCount);
                    $('#connectionsCount').html(response.countsArray.connectionsCount);
                },
                error: function(error) {
                    console.log(error);
                }
            });
        }
    });
</script>
