<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h2>Add User</h2>

    <form id="addUserForm">
        @csrf

        <label>Username:</label><br>
        <input type="text" name="username" id="username">
        <p class="error" id="username-error" style="color:red;"></p>

        <br><br>

        <label>Password:</label><br>
        <input type="password" name="password" id="password">
        <p class="error" id="password-error" style="color:red;"></p>

        <br><br>

        <label>Role:</label><br>
        <select name="role" id="role">
            <option value="user">User</option>
            <option value="admin">Admin</option>
        </select>
        <p class="error" id="role-error" style="color:red;"></p>

        <br><br>

        <button type="submit">Create</button>
        <p id="form-message"></p>
    </form>

    <script>
        const addUserUrl = "{{ route('add_user_process') }}";
        const usersPageUrl = "{{ route('admin_home') }}";
        const loginUrl = "{{ route('admin_login') }}";
        const token = localStorage.getItem('admin_token');

        // Kung walang token, balik agad sa login
        if (!token) {
            window.location.href = loginUrl;
        }

        const form = document.getElementById('addUserForm');

        form.addEventListener('submit', async function (e) {
            e.preventDefault();

            // Clear previous messages
            document.getElementById('username-error').textContent = '';
            document.getElementById('password-error').textContent = '';
            document.getElementById('role-error').textContent = '';
            document.getElementById('form-message').textContent = '';

            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value;
            const role = document.getElementById('role').value;

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

            try {
                const response = await fetch(addUserUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'Authorization': `Bearer ${token}`,
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    },
                    body: JSON.stringify({ username, password, role })
                });

                if (response.status === 401) {
                    localStorage.removeItem('admin_token');
                    window.location.href = loginUrl;
                    return;
                }

                const data = await response.json();

                if (!response.ok) {
                    if (data.errors) {
                        if (data.errors.username) {
                            document.getElementById('username-error').textContent = data.errors.username[0];
                        }
                        if (data.errors.password) {
                            document.getElementById('password-error').textContent = data.errors.password[0];
                        }
                        if (data.errors.role) {
                            document.getElementById('role-error').textContent = data.errors.role[0];
                        }
                    } else {
                        document.getElementById('form-message').style.color = 'red';
                        document.getElementById('form-message').textContent = data.message || 'Something went wrong.';
                    }
                    return;
                }

                document.getElementById('form-message').style.color = 'green';
                document.getElementById('form-message').textContent = data.message;
                form.reset();

                setTimeout(() => {
                    window.location.href = usersPageUrl;
                }, 1000);

            } catch (err) {
                document.getElementById('form-message').textContent = 'Network error. Please try again.';
            }
        });
    </script>
</body>
</html>