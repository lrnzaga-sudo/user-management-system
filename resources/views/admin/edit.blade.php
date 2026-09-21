<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h2>Edit</h2>

    <form id="editUserForm">
        @csrf

        <label>Username</label><br>
        <input type="text" name="username" id="username">
        <p class="error" id="username-error" style="color:red;"></p>

        <br><br>

        <label>Password (leave blank to keep current)</label><br>
        <input type="password" name="password" id="password">
        <p class="error" id="password-error" style="color:red;"></p>

        <br><br>

        <button type="submit">Update</button>
        <p id="form-message"></p>
    </form>

    <script>
        const userId = "{{ $id }}";
        const getUserUrl = "{{ route('get_user', 999999) }}".replace('999999', userId);
        const updateUrlTemplate = "{{ route('edit_user', 999999) }}";
        const updateUrl = updateUrlTemplate.replace('999999', userId);
        const usersPageUrl = "{{ route('admin_home') }}";
        const loginUrl = "{{ route('admin_login') }}";
        const token = localStorage.getItem('admin_token');

        if (!token) {
            window.location.href = loginUrl;
        }

        // Step 1: kunin ang user data at i-populate ang form
        async function loadUser() {
            try {
                const response = await fetch(getUserUrl, {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });

                if (response.status === 401) {
                    localStorage.removeItem('admin_token');
                    window.location.href = loginUrl;
                    return;
                }

                if (response.status === 404) {
                    document.getElementById('form-message').textContent = 'User not found.';
                    return;
                }

                const data = await response.json();
                document.getElementById('username').value = data.user.username;

            } catch (err) {
                document.getElementById('form-message').textContent = 'Failed to load user data.';
                console.error(err);
            }
        }

        // Step 2: i-submit ang update
        const form = document.getElementById('editUserForm');

        form.addEventListener('submit', async function (e) {
            e.preventDefault();

            document.getElementById('username-error').textContent = '';
            document.getElementById('password-error').textContent = '';
            document.getElementById('form-message').textContent = '';

            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value;

            if (username.length < 3) {
                document.getElementById('username-error').textContent = 'Username must be at least 3 characters.';
                return;
            }

            if (password && password.length < 8) {
                document.getElementById('password-error').textContent = 'Password must be at least 8 characters.';
                return;
            }

            const payload = { username };
            if (password) {
                payload.password = password;
            }

            try {
                const response = await fetch(updateUrl, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'Authorization': `Bearer ${token}`,
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    },
                    body: JSON.stringify(payload)
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
                    } else {
                        document.getElementById('form-message').style.color = 'red';
                        document.getElementById('form-message').textContent = data.message || 'Something went wrong.';
                    }
                    return;
                }

                document.getElementById('form-message').style.color = 'green';
                document.getElementById('form-message').textContent = data.message;

                setTimeout(() => {
                    window.location.href = usersPageUrl;
                }, 1000);

            } catch (err) {
                document.getElementById('form-message').textContent = 'Network error. Please try again.';
            }
        });

        // Initial load
        loadUser();
    </script>
</body>
</html>