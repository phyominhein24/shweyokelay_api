<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #4f46e5, #3b82f6);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .card {
            background: #fff;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        h2 {
            text-align: center;
            margin-bottom: 1.5rem;
            color: #333;
        }

        .form-control {
            margin-bottom: 15px;
        }

        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            transition: 0.3s;
        }

        input:focus {
            border-color: #3b82f6;
            outline: none;
            box-shadow: 0px 0px 6px rgba(59, 130, 246, 0.4);
        }

        button {
            width: 100%;
            padding: 12px;
            background: #3b82f6;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            background: #2563eb;
        }

        .alert-danger {
            background: #f87171;
            color: #fff;
            padding: 10px;
            margin-bottom: 1rem;
            border-radius: 6px;
            font-size: 14px;
        }

        .alert-success {
            background: #4ade80;
            color: #fff;
            padding: 10px;
            margin-bottom: 1rem;
            border-radius: 6px;
            font-size: 14px;
        }

        .text-small {
            text-align: center;
            margin-top: 1rem;
            font-size: 13px;
            color: #666;
        }
    </style>
</head>

<body>
    <div class="card">
        <h2>Reset Password</h2>

        {{-- Show success message --}}
        @if(session('status'))
            <div class="alert-success">{{ session('status') }}</div>
        @endif

        {{-- Show validation errors --}}
        @if ($errors->any())
            <div class="alert-danger">
                <ul style="margin:0; padding-left:18px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST">
            @csrf
            <input type="hidden" name="id" value="{{ $user[0]['id'] ?? '' }}" />

            <div class="form-control">
                <input type="password" name="password" placeholder="New Password" required />
            </div>

            <div class="form-control">
                <input type="password" name="password_confirmation" placeholder="Confirm Password" required />
            </div>

            <button type="submit">Reset Password</button>
        </form>

        <p class="text-small">After resetting, you can log in with your new password.</p>
    </div>
</body>

</html>
