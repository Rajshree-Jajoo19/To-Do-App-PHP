<!DOCTYPE html>
<html>
<head>
    <title>To-Do List</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
<style>
    #task-form {
            display: flex;
            justify-content: center;
            align-items: center;
        }
    .text-prop{
        display: flex;
        justify-content: center;
        align-items: center;
    }
</style>

</head>
<body>
<div class="container">
    <h2 class="my-4">To-Do List</h2>

    <!-- Task Form -->
    <form id="task-form" class="form-inline">
        @csrf
        <input type="text" id="task-name" class="form-control mb-2 mr-sm-2" placeholder="New Task" required>
        <button type="submit" class="btn btn-primary mb-2">Add Task</button> <br>
    </form>
    <div id="error-message" class="text-danger text-prop"></div><br>

    

    <!-- Show All Tasks Button -->
    <button id="show-all-tasks" class="btn btn-info mb-3">Show All Tasks</button>

    <!-- Task List -->
    <table class="table table-striped mt-3">
        <thead>
            <tr>
                <th>#</th>
                <th>Task</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="task-list">
            <!-- Tasks will be loaded here via AJAX when the button is clicked -->
        </tbody>
    </table>
</div>

<script>
    $(document).ready(function() {
        
        // Render task row function
// Render task row function
        function renderTask(task, index) {
            return `<tr id="task-${task.id}">
                <td>${index}</td>
                <td>${task.name}</td>
                <td>${task.status}</td>
                <td>
                    ${task.status === 'Pending' ? `<button class="btn btn-success btn-sm mark-complete" data-id="${task.id}">✔</button>` : ''}
                    <button class="btn btn-danger btn-sm delete-task" data-id="${task.id}">✖</button>
                </td>
            </tr>`;
        }


        // Load tasks on button click
        $('#show-all-tasks').click(function() {
            $.get('/tasks', function(response) {
                $('#task-list').html(''); // Clear the current task list
                if (response.status === 'success' && Array.isArray(response.tasks)) {
                    response.tasks.forEach(function(task, index) {
                        $('#task-list').append(renderTask(task, index + 1));
                    });
                } else {
                    $('#task-list').html('<tr><td colspan="4">No tasks available.</td></tr>');
                }
            }).fail(function(jqXHR, textStatus, errorThrown) {
                console.log('Error fetching tasks:', textStatus, errorThrown);
            });
        });

        // Add task via AJAX
        $('#task-form').on('submit', function(e) {
            e.preventDefault();
            const taskName = $('#task-name').val();

            $.post('/tasks', {name: taskName, _token: $('meta[name="csrf-token"]').attr('content')}, function(task) {
                $('#task-list').append(renderTask(task, $('#task-list tr').length + 1));
                $('#task-name').val('');
                $('#error-message').text('');
            }).fail(function(response) {
                $('#error-message').text(response.responseJSON.error);
            });
        });

        // Mark task as complete and remove it from the list
        $(document).on('click', '.mark-complete', function() {
            const taskId = $(this).data('id');
            $.ajax({
                url: `/tasks/${taskId}`,
                type: 'PUT',
                data: {_token: $('meta[name="csrf-token"]').attr('content')},
                success: function(response) {
                    // Remove the task row from the list after marking it complete
                    $(`#task-${taskId}`).remove();
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log('Error marking task complete:', textStatus, errorThrown);
                }
            });
        });

        // Delete task with confirmation
        $(document).on('click', '.delete-task', function() {
            if (confirm('Are you sure to delete this task?')) {
                const taskId = $(this).data('id');
                $.ajax({
                    url: `/tasks/${taskId}`,
                    type: 'DELETE',
                    data: {_token: $('meta[name="csrf-token"]').attr('content')},
                    success: function() {
                        $(`#task-${taskId}`).remove(); // Remove the task from the UI after deletion
                    }
                });
            }
        });

    });
</script>
</body>
</html>
