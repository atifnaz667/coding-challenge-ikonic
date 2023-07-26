@foreach ($connections as $connection)
<div class="p-2 shadow rounded mt-2  text-white bg-dark">{{ $connection->name }} - {{ $connection->email }}</div>
@endforeach
