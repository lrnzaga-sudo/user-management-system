<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h2>User Log In</h2>

    <form id="loginForm">
        @csrf

        <label>Username:</label><br>
        <input type="text" name="username" id="username">
        <p class="error" id="username-error" style="color:red;"></p>

        <br><br>

        <label>Password:</label><br>
        <input type="password" name="password" id="password">
        <p class="error" id="password-error" style="color:red;"></p>

        <br><br>

        <button type="submit">Log In</button>
        <p id="form-message"></p>
    </form>

    <a href="{{ route('user_register') }}">
        <button type="button">Register</button>
    </a>

    <script>
        const loginUrl = "{{ route('user_login_process') }}";
        const homeUrl = "{{ route('user_home') }}";

        const form = document.getElementById('loginForm');

        form.addEventListener('submit', async function (e) {
            e.preventDefault();

            // Clear previous messages
            document.getElementById('username-error').textContent = '';
            document.getElementById('password-error').textContent = '';
            document.getElementById('form-message').textContent = '';

            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value;

            // Basic client-side validation
            let hasError = false;

            if (!username) {
                document.getElementById('username-error').textContent = 'Username is required.';
                hasError = true;
            }

            if (!password) {
                document.getElementById('password-error').textContent = 'Password is required.';
                hasError = true;
            }

            if (hasError) return;

            try {
                const response = await fetch(loginUrl, {
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
                    if (data.errors) {
                        if (data.errors.username) {
                            document.getElementById('username-error').textContent = data.errors.username[0];
                        }
                        if (data.errors.password) {
                            document.getElementById('password-error').textContent = data.errors.password[0];
                        }
                    } else {
                        document.getElementById('form-message').style.color = 'red';
                        document.getElementById('form-message').textContent = data.message || 'Login failed.';
                    }
                    return;
                }

                // Success: store token and redirect
                localStorage.setItem('user_token', data.token);

                document.getElementById('form-message').style.color = 'green';
                document.getElementById('form-message').textContent = data.message;

                setTimeout(() => {
                    window.location.href = homeUrl;
                }, 1000);

            } catch (err) {
                document.getElementById('form-message').textContent = 'Network error. Please try again.';
            }
        });
    </script>
</body>
</html>