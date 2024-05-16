<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Image Gallery Prototype</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Play:wght@400;700&display=swap" rel="stylesheet">

        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    </head>
    <body class="font-sans antialiased dark:bg-black dark:text-white/50">
        <h1>Another test task for a job position</h1>
        <form action="/uploadImages" method="post" enctype="multipart/form-data" id="uploadForm">
            @csrf
            <input type="file" name="images[]" multiple id="imageInput">
            <button type="submit">Upload</button>
        </form>
        <div class="sort-btns">
            <script>
                var sortField = "{{ request()->query('sort_field') ?? 'uploaded_at' }}";
                var sortOrder = "{{ request()->query('sort_order') ?? 'desc' }}";
            </script>
            Sort by:
            <button onclick="toggleSort('uploaded_at')" class="sort-btn">Time</button>
            <button onclick="toggleSort('filename')" class="sort-btn">Name</button>
        </div>
        <div class="image-list">
            @foreach ($images as $image)
                <div class="image">
                    <button class="image-btn">
                        <img src="/images/{{ $image->filename }}" alt="{{ $image->filename }}">
                    </button>
                    <span>Uploaded At: {{ $image->uploaded_at }}</span>
                    <button onclick="downloadImage({{ $image->id }})">Download</button>
                </div>
            @endforeach
        </div>
        <script>
        // max image check
        document.getElementById('imageInput').addEventListener('change', function() {
            var maxFiles = 5;
            if(this.files.length > maxFiles){
                alert("You can't select more than " + maxFiles + " files.");
                this.value = '';
            }
        });

        function toggleSort(field) {
            sortOrder = sortOrder === 'desc' ? 'asc' : 'desc';
            window.location.href = `/?sort_field=${field}&sort_order=${sortOrder}`;
        }

        async function downloadImage(id) {
            const url = `/download-zip/${id}`;
            try {
                const response = await fetch(url);

                if (!response.ok) {
                    throw new Error(`HTTP error status: ${response.status}`);
                }

                const blob = await response.blob();

                const link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob);
                link.download = 'image.zip';

                document.body.appendChild(link);
                link.click();

                document.body.removeChild(link);
            } catch (error) {
                console.error('Error downloading images:', error);
            }
        }

        // popup for a full image
        document.addEventListener('DOMContentLoaded', function() {
            const buttons = document.querySelectorAll('.image-btn');

            buttons.forEach(function(button) {
                button.addEventListener('click', function() {
                    const imgSrc = this.querySelector('img').getAttribute('src');

                    const fullSizeImg = document.createElement('img');
                    fullSizeImg.src = imgSrc;

                    const fullSizeContainer = document.createElement('div');
                    fullSizeContainer.classList.add('fullsize-container');

                    fullSizeContainer.appendChild(fullSizeImg);

                    document.body.appendChild(fullSizeContainer);

                    fullSizeContainer.addEventListener('click', function() {
                        document.body.removeChild(fullSizeContainer);
                    });
                });
            });
        });
        </script>
    </body>
</html>
