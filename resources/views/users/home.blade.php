<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h2>Home</h2>

    <button id="logoutBtn">Log Out</button>
    <br>
    <button id="editBtn">Edit</button>

    <p>Hello <span id="usernameDisplay">...</span></p>

    <script>
        const token = localStorage.getItem('user_token');
        const loginUrl = "{{ route('user_login') }}";
        const logoutUrl = "{{ route('user_logout') }}";
        const editUrl = "{{ route('edit_user_page') }}";
        const getUserUrl = "{{ route('get_current_user') }}"; // adjust to your actual route name

        // Step 1: kung walang token, balik agad sa login
        if (!token) {
            window.location.href = loginUrl;
        }

        // Step 2: kunin ang current user info gamit ang token
        async function loadUser() {
            try {
                const response = await fetch(getUserUrl, {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });

                if (response.status === 401) {
                    localStorage.removeItem('user_token');
                    window.location.href = loginUrl;
                    return;
                }

                const data = await response.json();
                document.getElementById('usernameDisplay').textContent = data.user.username;

            } catch (err) {
                console.error('Failed to load user info:', err);
            }
        }

        // Step 3: logout
        document.getElementById('logoutBtn').addEventListener('click', async function () {
            try {
                await fetch(logoutUrl, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });
            } catch (err) {
                console.error(err);
            } finally {
                localStorage.removeItem('user_token');
                window.location.href = loginUrl;
            }
        });

        // Step 4: navigate to edit page
        document.getElementById('editBtn').addEventListener('click', function () {
            window.location.href = editUrl;
        });



        // Initial load
        loadUser();
    </script>
</body>
</html>