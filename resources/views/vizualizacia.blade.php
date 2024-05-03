@extends('layouts.app')

@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Posts List</title>
    <link href="/css/vizualizacia.css" rel="stylesheet">
</head>
<body>
<div class="sidebar">
    <input type="text" id="searchPosts" placeholder="Search posts..." class="form-control mb-2">
    <span id="clearSearch">Clear</span>
    <ul id="postsList" class="list-unstyled mt-3">
    @foreach($posts as $post)
        <li class="posts-item" data-post-id="{{ $post['post_id'] }}">{{ $post['title'] }}</li>
    @endforeach
    </ul>
</div>
<div class="content">
    <!-- Main content area, e.g., post details -->
    <div class="container2">
        <div id="chartHeader" class="chart-header">
            <h2>Vitajte vo vizualizácii názorov</h2>
            <p>Pre zobrazenie grafu rozloženia vyberte tému na ľavej strane obrazovky</p>
        </div>
        <canvas id="commentsChart"></canvas>
        <button id="showMoreBtn" class="btn btn-primary">Zobraziť viac</button>
        <div id="commentsTableContainer" class="comments-table-container" style="display:none;"></div>
    </div>
    
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let commentsChart = null;
document.getElementById('clearSearch').addEventListener('click', function() {
    document.getElementById('searchPosts').value = '';
    filterPosts(); // Call filterPosts to reset the filter
});

function filterPosts() {
    let input = document.getElementById('searchPosts');
    let filter = input.value.toUpperCase();
    let ul = document.getElementById("postsList");
    let li = ul.getElementsByTagName('li');

    for (let i = 0; i < li.length; i++) {
        let item = li[i];
        let text = item.textContent || item.innerText;
        if (text.toUpperCase().indexOf(filter) > -1) {
            li[i].style.display = "";
        } else {
            li[i].style.display = "none";
        }
    }
}

// Automatically attach the filterPosts function to the search input
document.getElementById('searchPosts').addEventListener('keyup', filterPosts);

let currentPostId = null; // This will store the currently selected post ID

// Add event listener to each post item to display the graph
let postItems = document.querySelectorAll('.posts-item');
postItems.forEach(item => {
    item.addEventListener('click', function() {
        const topicTitle = this.innerText;
        currentPostId = this.getAttribute('data-post-id');
        console.log('Selected Post ID:', currentPostId);
        document.querySelector('#chartHeader h2').innerText = topicTitle;
        document.querySelector('#chartHeader p').innerText = 'Graf rozloženia pre vybranú tému:';
        let postId = item.getAttribute('data-post-id');
        console.log('Displaying graph for post ID :', postId);
        displayGraph(postId);
    });
});

function displayGraph(postId) {
    if (postId) {
        fetch(`/show?id=${postId}`)
            .then(response => {
                if (response.ok) {
                    return response.json();
                }
                throw new Error('Network response was not ok.');
            })
            .then(data => {
                // Check if data contains labels and data arrays
                console.log('Labels:', data.labels);
                console.log('Data:', data.data);
                console.log('Raw data:', data);
                if (data.labels && data.data) {
                    // Update the chart with the fetched data
                    updateChart(data.labels, data.data);
                } else {
                    console.error('Invalid data format:', data);
                }
            })
            .catch(error => {
                console.error('Error fetching data:', error);
            });
    } else {
        console.error('Invalid post ID');
    }
}

function updateChart(labels, data) {
    console.log('Labels UpdateChart:', labels);
    console.log('Data UpdateChart:', data);
    if (commentsChart) {
        commentsChart.destroy(); // Destroy the previous chart instance if it exists
    }

    var ctx = document.getElementById('commentsChart').getContext('2d');
    // Removed 'var' to ensure commentsChart is treated as the global variable
    commentsChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Number of Comments',
                data: data,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    // more colors as needed
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    // more colors as needed
                ],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}

document.getElementById('showMoreBtn').addEventListener('click', function() {
    let tableContainer = document.getElementById('commentsTableContainer');
    if (tableContainer.style.display === "none" || tableContainer.style.display === "") {
        tableContainer.style.display = "block"; // Show table
        displayTable(currentPostId); // Call displayTable only when showing the table
    } else {
        tableContainer.style.display = "none"; // Hide table
    }
});

function displayTable(postId) {
    console.log('111',postId);
    if (postId) {
        fetch(`/showComments?id=${postId}`) // Adjusted to match the Laravel route
        .then(response => response.json())
        .then(data => {
            console.log('Data received:', data);
            console.log('data.commentsData:', data.commentsData);
            if (data.commentsData) {
                updateCommentsTable(data.commentsData); // Prepare comments table
            } else {
                console.error('No data found for comments');
            }
        })
        .catch(error => console.error('Error:', error));
    } else {
        console.error('No postId provided for the table display');
    }
}

function stringToColor(str) {
    let hash = 0;
    for (let i = 0; i < str.length; i++) {
        hash = str.charCodeAt(i) + ((hash << 5) - hash);
    }
    let color = '#';
    for (let i = 0; i < 3; i++) {
        let value = (hash >> (i * 8)) & 0xFF;
        color += ('00' + value.toString(16)).substr(-2);
    }
    return color;
}

function updateCommentsTable(commentsData) {
    let tableHTML = "<table class='table'><tr><th>Názory</th><th>Pod-témy</th></tr>";
    commentsData.forEach(comment => {
        let tagsHTML = comment.topics.map(topic => {
            const color = stringToColor(topic);
            return `<div class='tag-badge' style='background-color: ${color};'>${topic}</div>`;
        }).join(' ');
        tableHTML += `<tr><td>${comment.text}</td><td>${tagsHTML}</td></tr>`;
    });
    tableHTML += "</table>";

    document.getElementById('commentsTableContainer').innerHTML = tableHTML;
}

</script>
</body>
</html>
@endsection
