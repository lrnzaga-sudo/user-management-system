<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h2>Users</h2>

    <button id="logoutBtn">Log Out</button>

    <a href="{{ route('add_user_page') }}">
        <button type="button">Add User</button>
    </a>

    <form id="searchForm">
        <input type="text" id="searchInput" placeholder="Search ID">
        <button type="submit">Search</button>
    </form>

    <table border="1">
        <thead>
            <tr>
                <th>User ID</th>
                <th>Username</th>
                <th>Edit</th>
                <th>Delete</th>
            </tr>
        </thead>
        <tbody id="usersTableBody">
            <tr><td colspan="4">Loading...</td></tr>
        </tbody>
    </table>

    <p id="errorMessage" style="color:red;"></p>

    <script>
        const token = localStorage.getItem('admin_token');
        const loginUrl = "{{ route('admin_login') }}";
        const logoutUrl = "{{ route('admin_logout') }}";
        const usersDataUrl = "/api/admin/users-data";
        const searchUrl = "{{ route('user_search_process') }}";
        const updateUserRouteTemplate = "{{ route('view_edit_user', 999999) }}";
        const updateUserRoute = (id) => updateUserRouteTemplate.replace('999999', id);
        const deleteUserRouteTemplate = "{{ route('delete_user_process', 999999) }}";
        const deleteUserRoute = (id) => deleteUserRouteTemplate.replace('999999', id);

        // Step 1: kung walang token, balik agad sa login
        if (!token) {
            window.location.href = loginUrl;
        }

        // Step 2: fetch users
        async function loadUsers(url = usersDataUrl) {
            try {
                const response = await fetch(url, {
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

                const result = await response.json();
                renderUsers(result.users);
            } catch (err) {
                document.getElementById('errorMessage').textContent = 'Failed to load users.';
                console.error(err);
            }
        }

        function renderUsers(users) {
            const tbody = document.getElementById('usersTableBody');

            if (!users || users.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4">No users found.</td></tr>';
                return;
            }

            tbody.innerHTML = users.map(user => `
                <tr>
                    <td>${user.id}</td>
                    <td>${user.username}</td>
                    <td><button onclick="window.location.href='${updateUserRoute(user.id)}'">Edit</button></td>
                    <td><button onclick="deleteUser(${user.id})">Delete</button></td>
                </tr>
            `).join('');
        }

        // Step 3: delete user
        async function deleteUser(id) {
            if (!confirm('Are you sure you want to delete this user?')) return;

            try {
                const response = await fetch(deleteUserRoute(id), {
                    method: 'DELETE',
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

                if (response.ok) {
                    loadUsers(); // refresh list
                } else {
                    alert('Failed to delete user.');
                }
            } catch (err) {
                console.error(err);
            }
        }

        // Step 4: search by ID
        document.getElementById('searchForm').addEventListener('submit', function (e) {
            e.preventDefault();
            console.log('Search submitted!'); // ADD THIS
            const query = document.getElementById('searchInput').value.trim();
            console.log('Query:', query); // ADD THIS
            if (query) {
                loadUsers(`${searchUrl}?search=${encodeURIComponent(query)}`);
            } else {
                loadUsers();
            }
        });
        // Step 5: logout
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
                localStorage.removeItem('admin_token');
                window.location.href = loginUrl;
            }
        });

        // Initial load
        loadUsers();
    </script>
</body>
</html>