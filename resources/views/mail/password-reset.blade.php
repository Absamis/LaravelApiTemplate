<p style="font-weight: bold">Dear {{ $user['name'] }}</p>
<p>Verify you account for password recovery</p>
<p style="text-align: center">
    <a href="{{ $data['verifyUrl'] }}" style="padding: 10px;font-size: 18px; background-color: rgb(57, 209, 57);">
        Verify Account
    </a>
</p>
