// Fetch data from the Flask endpoint
fetch('/fetch_data')
    .then(response => response.json())
    .then(data => {
        // Populate Users table
        const usersTableBody = document.getElementById('usersTableBody');
        data.users.forEach(user => {
            const row = `<tr>
                <td>${user.id}</td>
                <td>${user.name}</td>
                <td>${user.email}</td>
            </tr>`;
            usersTableBody.innerHTML += row;
        });

        // Populate Activity table
        const activityTableBody = document.getElementById('activityTableBody');
        data.activity.forEach(activity => {
            const row = `<tr>
                <td>${activity.id}</td>
                <td>${activity.action}</td>
                <td>${activity.timestamp}</td>
            </tr>`;
            activityTableBody.innerHTML += row;
        });
    })
    .catch(error => console.error('Error fetching data:', error));

// Function to show the Users table
function showUsersTable() {
    document.getElementById('activityTable').style.display = 'none';
    document.getElementById('usersTable').style.display = 'table';
}

// Function to show the Activity table
function showActivityTable() {
    document.getElementById('usersTable').style.display = 'none';
    document.getElementById('activityTable').style.display = 'table';
}