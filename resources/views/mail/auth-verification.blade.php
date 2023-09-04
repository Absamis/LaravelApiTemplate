<!DOCTYPE html>
<html>
    <head></head>
    <body>
        <h5>Dear {{$user['name']}},</h5>
        <p>{{$data['message']}}</p>
        @if ($data['type'] == 'link')
            <a href="{{$data['type']}}">Verify</a>
        @else
            <h3 style="text-align: center;">{{$data["code"]}}</h3>
        @endif
    </body>
</html>
