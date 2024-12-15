<!DOCTYPE html>
<html lang="en">

<head>
    <title>Reset Password</title>
</head>

<style>
    .form-control {
        margin-bottom: 10px;
    }

    .alert-danger {
        background: red;
    }
</style>

<body>

    <form method='POST'>
        @csrf
        <input type="hidden" name="id" value="{{ $user[0]['id'] }}" />
        <div class="form-control">
            <input type="password" name="password" placeholder="New Password" />
            {{-- @error('password')
                <p>{{ $message }}</p>
            @enderror --}}
        </div>
        <div class="form-control">
            <input type="password" name="password_confirmation" placeholder="Confirm Password" />
        </div>
        <button type="submit">
            Reset
        </button>

    </form>
</body>

</html>
