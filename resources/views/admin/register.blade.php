<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h2>Admin Register</h2>

    <form id="registerForm" action="{{ route('admin.register') }}" method="POST">
        @csrf

        <!-- <input type="hidden" id="role" name="role" value="admin"> -->

        <label>Username:</label><br>
        <input type="text" name="username" id="username">
        <p class="error" id="username-error" style="color:red;"></p>

        <br><br>

        <label>Password:</label><br>
        <input type="password" name="password" id="password">
        <p class="error" id="password-error" style="color:red;"></p>

        <br><br>

        <button type="submit">Register</button>
        <p id="form-message"></p>
    </form>

    <a href="{{ route('admin_login') }}">
        <button type="button">Log In</button>
    </a>

    <script>
        const form = document.getElementById('registerForm');

        form.addEventListener('submit', async function (e) {
            e.preventDefault();

            // Clear previous errors
            document.getElementById('username-error').textContent = '';
            document.getElementById('password-error').textContent = '';
            document.getElementById('form-message').textContent = '';

            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value;

            // Client-side validation
            let hasError = false;

            if (username.length < 3) {
                document.getElementById('username-error').textContent = 'Username must be at least 3 characters.';
                hasError = true;
            }

            if (password.length < 8) {
                document.getElementById('password-error').textContent = 'Password must be at least 8 characters.';
                hasError = true;
            }

            if (hasError) return;

            // AJAX submission
            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    },
                    body: JSON.stringify({ username, password })
                });

                const data = await response.json();

                if (!response.ok) {
                    // Laravel validation errors come back as { errors: { field: [messages] } }
                    if (data.errors) {
                        if (data.errors.username) {
                            document.getElementById('username-error').textContent = data.errors.username[0];
                        }
                        if (data.errors.password) {
                            document.getElementById('password-error').textContent = data.errors.password[0];
                        }
                    } else {
                        document.getElementById('form-message').textContent = data.message || 'Something went wrong.';
                    }
                    return;
                }

                document.getElementById('form-message').style.color = 'green';
                document.getElementById('form-message').textContent = data.message;
                form.reset();

                // Redirect to login after a short delay
                setTimeout(() => {
                    window.location.href = "{{ route('admin_login') }}";
                }, 1500); // 1.5 second delay so user sees the success message

            } catch (err) {
                document.getElementById('form-message').textContent = 'Network error. Please try again.';
            }
        });
    </script>
</body>
</html>