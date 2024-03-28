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

// Add event listener to each post item to display the graph
let postItems = document.querySelectorAll('.posts-item');
postItems.forEach(item => {
    item.addEventListener('click', function() {
        const topicTitle = this.innerText;
        document.querySelector('#chartHeader h2').innerText = topicTitle;
        document.querySelector('#chartHeader p').innerText = 'Graf rozloženia pre vybranú tému:';
        let postId = item.getAttribute('data-post-id');
        console.log('Displaying graph for post ID 111:', postId);
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
</script>
</body>
</html>
@endsection
