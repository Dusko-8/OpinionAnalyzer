@extends('layouts.app')

@section('content')
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/analyza.css">
    <title>Posts Dropdown</title>
</head>
<body>
    <div id="selectPostsContainer">
        <h1 class="page-title">Analýza dát</h1>
        <div id="selectPosts">
            <label for="postSelect">Vyberte sociálnu otázku:</label>
            <select id="postSelect">
                <option value="" disabled selected>Vyberte</option>
                @foreach ($posts as $post)
                    <option value="{{ $post->post_id }}"
                            data-topics="{{ json_encode($post->topics) }}"
                            data-unanalyzed-count="{{ $post->unanalyzed_comments_count }}">
                        {{ $post->title }}
                    </option>
                @endforeach
            </select>
        </div>
        <label for="topicsContainer">Kategorizačné štítky pre tému:</label>
        <div id="topicsContainer"></div>
        <div id="newTopicsContainer" style="display: none;">
            <p>Create new topics for this post:</p>
            <div id="newTopicsInputContainer">
            </div>
        </div>
        <button id="SugestSubtopics" style="display: none;" onclick="SugestSubtopics()">Automatické navrhnutie štítkov</button>
        <div id="buttonsContainer">
            <button id="analyzeOpinions" onclick="analyzeOpinionsWithCurrentTopics()">Analyzuj názory na zvolenú tému</button>
            <a href="{{ route('vizualizacia') }}" id="Vyzualizacia">Pokračuj na vizualizáciu</a>
        </div>
    </div>
</body>
</html>
<script>
    document.getElementById('postSelect').addEventListener('change', function() {
        var selectedPost = this.options[this.selectedIndex];
        var postId = selectedPost.value;
        // Update the button text
        var unanalyzedCount = selectedPost.getAttribute('data-unanalyzed-count');

        // Now update the button text with the defined unanalyzedCount
        const analyzeButton = document.getElementById('analyzeOpinions');
        analyzeButton.innerHTML = `Analyzuj názory na zvolenú tému <span class="green-text">(${unanalyzedCount} neanalyzovaných)</span>`;

        var postText = selectedPost.text; // Get the text of the selected post
        var topics = selectedPost.getAttribute('data-topics') ? JSON.parse(selectedPost.getAttribute('data-topics')) : [];
        
        var topicsContainer = document.getElementById('topicsContainer');
        var newTopicsContainer = document.getElementById('newTopicsContainer');
        topicsContainer.innerHTML = '';
        newTopicsContainer.innerHTML = '';
        
        if (topics.length === 0) {
            newTopicsContainer.style.display = 'block';
            for (let i = 0; i < 7; i++) {
                var input = document.createElement('input');
                input.type = 'text';
                input.placeholder = 'Topic ' + (i + 1);
                input.className = 'new-topic-input';
                if (i === 6) { // Set the last input to "Ostatné"
                    input.value = 'Ostatné';
                }
                newTopicsContainer.appendChild(input);
            }

            var saveButton = document.createElement('button');
            saveButton.textContent = 'Uložiť štítky témy';
            saveButton.className = 'wide-button'; 
            saveButton.onclick = function() {
                saveTopics(postId);
            };
            newTopicsContainer.appendChild(saveButton);
        
            // Add event listeners after creating inputs
            document.querySelectorAll('.new-topic-input').forEach(input => {
                input.addEventListener('input', function() {
                    let inputs = document.querySelectorAll('.new-topic-input');
                    let emptyCount = Array.from(inputs).slice(0, -1).filter(i => i.value.trim() === '').length;
                    let ostatneInput = inputs[inputs.length - 1];
                    let controllerButton = document.getElementById('SugestSubtopics');
                
                    if (emptyCount === 6 && ostatneInput.value.trim() === 'Ostatné') {
                        controllerButton.style.display = 'block';
                    } else {
                        controllerButton.style.display = 'none';
                    }
                });
            });

            // Set onclick event for the controllerButton with the current selectedPost's text
            var controllerButton = document.getElementById('SugestSubtopics');
            controllerButton.onclick = function() {
                SugestSubtopics(postText); // Pass the selected post's text to the function
            };

            // Manually trigger the input event for validation
            let event = new Event('input', { bubbles: true });
            document.querySelectorAll('.new-topic-input').forEach(input => input.dispatchEvent(event));
        } else {
            newTopicsContainer.style.display = 'none';
            topics.slice(0, 7).forEach(function(topic) {
                var div = document.createElement('div');
                div.classList.add('topic');
                div.textContent = topic.topic_name;
                topicsContainer.appendChild(div);
            });
        }
    });

    function saveTopics(postId) {
        var topicInputs = document.querySelectorAll('.new-topic-input');
        var topics = Array.from(topicInputs).map(input => input.value.trim());

        if (topics.length === 7 && topics.every(topic => topic !== '')) {
            fetch('/save-topics', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ postId: postId, topics: topics })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                console.log('Success:', data);
                // Handle success
            })
            .catch((error) => {
                console.error('Error:', error);
                // Handle errors
            });
        } else {
            alert('Please fill in all topic fields.');
        }
    }
    
    function SugestSubtopics(selectedPostText) {
        const postData = {
            postText: selectedPostText // Passing the selected post text to the backend
        };

        console.log('Posting data:', postData); // Print the argument being passed

        fetch('/Sugest-Subtopics', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(postData)
        })
        .then(response => response.json())
        .then(data => {
            console.log('Success:', data);

            // Check if subtopics were successfully received
            if(data.success && data.subtopics.length > 0) {
                // Assuming the input fields have a class name 'new-topic-input'
                const inputs = document.querySelectorAll('.new-topic-input');

                data.subtopics.forEach((subtopic, index) => {
                    // Ensure we don't try to fill more inputs than we have
                    if(index < inputs.length) {
                        inputs[index].value = subtopic;
                    }
                });
            } else {
                // Handle the case where no subtopics are received or there was an error
                console.error('No subtopics received or there was an error');
            }
        })
        .catch((error) => {
            console.error('Error:', error);
        });
    }

    function analyzeOpinionsWithCurrentTopics() {
        var loadingIndicator = document.getElementById('loadingIndicator');
        var successMessageContainer = document.getElementById('successMessage');
        
        // Ensure the loading indicator and success message container exist
        if (!loadingIndicator) {
            loadingIndicator = document.createElement('div');
            loadingIndicator.id = 'loadingIndicator';
            loadingIndicator.textContent = 'Analyzujem...';
            document.body.appendChild(loadingIndicator);
        }
        if (!successMessageContainer) {
            successMessageContainer = document.createElement('div');
            successMessageContainer.id = 'successMessage';
            document.body.appendChild(successMessageContainer);
        }

        // Show loading indicator and hide previous success message
        loadingIndicator.style.display = 'block';
        successMessageContainer.style.display = 'none';

        var postId = document.getElementById('postSelect').value;
        // Find the selected option element
        var selectedOption = document.getElementById('postSelect').querySelector('option:checked');
        // Parse the data-topics attribute of the selected option
        var topics = selectedOption.getAttribute('data-topics') ? JSON.parse(selectedOption.getAttribute('data-topics')) : [];
        // Extract the topic IDs from the parsed topics array
        var topicIds = topics.map(function(topic) {
            return topic.topic_id; // Adjust 'topic_id' if the property name is different in your topics JSON
        });
    
        console.log("Topic IDs:", topicIds);

        var requestData = {
            post_id: postId,
            topic_ids: topicIds,
        };

        console.log("Sending request data:", requestData);

        fetch('/getCommentsFilteredByTopics', { // Ensure this endpoint matches your route definition
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(requestData)
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not OK');
            }
            return response.json();
        })
        .then(data => {
            console.log("Fetch request successful. Response data:", data);
            // Hide loading indicator and show success message
            loadingIndicator.style.display = 'none';
            successMessageContainer.textContent = 'kategorizácia prevedená úspešne.';
            successMessageContainer.style.display = 'block';
            // Process the data here, such as updating the DOM with the comments
        })
        .catch(error => {
            console.error("Fetch request failed:", error);
            loadingIndicator.style.display = 'none';
        });
    }

</script>

@endsection
