<?php
include 'db.php';
include 'mailer.php';
session_start();

// Ensure student is logged in, otherwise redirect to login page
if(!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ConcernHub - Submit Concern</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --sidebar-bg: #1a1c2e;
            --primary-purple: #5d5fef;
            --bg-light: #f9f9fb;
        }
        body { background-color: var(--bg-light); font-family: 'Inter', sans-serif; }
        
        /* Sidebar Styles */
        .sidebar { background-color: var(--sidebar-bg); min-height: 100vh; color: white; width: 260px; position: fixed; z-index: 1000; }
        .nav-link { color: #a0a0b0; padding: 12px 20px; border-radius: 10px; margin: 5px 15px; transition: 0.3s; }
        .nav-link.active { background-color: var(--primary-purple); color: white; }
        .nav-link:hover:not(.active) { background-color: rgba(255,255,255,0.05); color: white; }

        /* Main Content Area */
        .main-content { margin-left: 260px; padding: 40px; }
        
        /* Category Cards */
        .card-container { max-width: 800px; background: white; border-radius: 20px; padding: 40px; border: 1px solid #edf0f5; margin: 0 auto; }
        
        .cat-card {
            border: 1px solid #f0f0f0; border-radius: 12px; padding: 15px; margin-bottom: 10px;
            cursor: pointer; transition: 0.2s; position: relative; display: flex; 
            flex-direction: column; align-items: center; justify-content: center; text-align: center;
            height: 100%; min-height: 160px; /* Tinaasan para magkasya ang malaking icon */
        }
        .cat-card:hover { background-color: #f8f9ff; border-color: var(--primary-purple); }
        .cat-card.selected { background-color: #f0f1ff; border-color: var(--primary-purple); }
        .cat-card input { position: absolute; opacity: 0; }
        
        .icon-circle { 
            width: 60px; height: 60px; border-radius: 50%; display: flex; 
            align-items: center; justify-content: center; margin-bottom: 15px; font-size: 28px; flex-shrink: 0;
        }
        .bg-academic { background: #eef2ff; color: #5d5fef; }
        .bg-financial { background: #ecfdf5; color: #10b981; }
        .bg-welfare { background: #fff1f2; color: #f43f5e; }

        .btn-continue { background-color: var(--primary-purple); border: none; padding: 12px; border-radius: 8px; font-weight: 600; width: 100%; transition: 0.3s; color: white; }
        .btn-continue:hover { background-color: #4a4cd9; color: white; }
        
        .form-label { font-size: 12px; font-weight: 700; color: #6c757d; text-transform: uppercase; letter-spacing: 0.5px; }
        .form-control, .form-select { padding: 10px 15px; border-radius: 8px; border: 1px solid #e0e0e0; }
        .form-control:focus { border-color: var(--primary-purple); box-shadow: 0 0 0 3px rgba(93, 95, 239, 0.1); }
    </style>
</head>
<body>

<div class="sidebar d-flex flex-column p-3">
    <div class="d-flex align-items-center mb-5 px-3">
        <div class="bg-primary rounded p-2 me-2"><i class="fas fa-graduation-cap text-white"></i></div>
        <div>
            <div class="fw-bold lh-1">ConcernHub</div>
            <small class="text" style="font-size: 10px;">Student Helpdesk</small>
        </div>
    </div>
    <ul class="nav flex-column mb-auto">
        <li class="nav-item"><a href="index.php" class="nav-link active"><i class="fas fa-edit me-2"></i> Submit Concern</a></li>
        <li class="nav-item"><a href="my_concerns.php" class="nav-link"><i class="fas fa-folder me-2"></i> My Concerns</a></li>
    </ul>
    <div class="mt-auto px-3 py-4 border-top border-secondary">
        <div class="d-flex align-items-center mb-3">
            <div class="bg-primary text-white rounded-circle me-2" style="width: 35px; height: 35px; display: flex; align-items:center; justify-content:center;"><?php echo strtoupper(substr($_SESSION['student_name'], 0, 1)); ?></div>
            <div style="font-size: 12px;">
                <div class="fw-bold"><?php echo htmlspecialchars($_SESSION['student_name']); ?></div>
                <div class="text-white-50"><?php echo htmlspecialchars($_SESSION['student_email']); ?></div>
            </div>
        </div>
        <a href="student_logout.php" class="text-white-50 text-decoration-none small"><i class="fas fa-sign-out-alt me-1"></i> Sign Out</a>
    </div>
</div>

<div class="main-content">
    <div class="text-center mb-4">
        <h3 class="fw-bold">Submit a Concern</h3>
        <p class="text-muted small">Your concern will be routed to the appropriate department automatically.</p>
    </div>

    <div class="container">
        <div class="card-container shadow-sm">
            <form action="process_concern.php" method="POST" enctype="multipart/form-data">
                
                <!-- Step 1: Category -->
                <h5 class="fw-bold mb-3 text-dark">Select Category</h5>
                <div class="row mb-4">
                    <div class="col-md-4">
                        <label class="cat-card w-100" onclick="select(this)">
                            <input type="radio" name="category" value="Academic" required>
                            <div class="icon-circle bg-academic"><i class="fas fa-graduation-cap"></i></div>
                            <div class="fw-bold small">Academic</div>
                        </label>
                    </div>
                    <div class="col-md-4">
                        <label class="cat-card w-100" onclick="select(this)">
                            <input type="radio" name="category" value="Financial">
                            <div class="icon-circle bg-financial"><i class="fas fa-money-bill-wave"></i></div>
                            <div class="fw-bold small">Financial</div>
                        </label>
                    </div>
                    <div class="col-md-4">
                        <label class="cat-card w-100" onclick="select(this)">
                            <input type="radio" name="category" value="Welfare">
                            <div class="icon-circle bg-welfare"><i class="fas fa-heart"></i></div>
                            <div class="fw-bold small">Welfare</div>
                        </label>
                    </div>
                </div>

                <!-- Step 2: Details -->
                <h5 class="fw-bold mb-3 text-dark">Concern Details</h5>
                
                <div class="mb-3">
                    <label class="form-label">SPECIFIC CONCERN TYPE</label>
                    <select name="title" class="form-select" required>
                        <option value="" disabled selected> Select a subcategory </option>
                    </select>
               </div>

                <div class="mb-3">
                    <label class="form-label">PRIORITY LEVEL</label>
                    <select name="priority" class="form-select" required>
                        <option value="" disabled selected> Select a priority level </option>
                        <option value="Low">Low - No Immediate Action Needed </option>
                        <option value="Medium">Medium - Needs Action Soon </option>
                        <option value="Urgent">Urgent - Immediate Action Needed </option>
                    </select>
               </div>

                <div class="mb-3">
                    <textarea name="description" class="form-control" rows="5" placeholder="Please describe your concern in detail..." required></textarea>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Your Email</label>
                        <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($_SESSION['student_email']); ?>" readonly required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Attachment (Optional)</label>
                        <input type="file" name="attachment" class="form-control">
                    </div>
                </div>

                <div class="form-check mb-4 p-3 bg-light rounded border">
                    <input type="checkbox" name="anonymous" class="form-check-input" id="anonCheck">
                    <label class="form-check-label small" for="anonCheck">
                        <strong>Submit Anonymously</strong><br>
                        <span class="text-muted">Your name will be hidden from the department staff.</span>
                    </label>
                </div>

                <button type="submit" class="btn btn-continue"> Submit Ticket <i class="fas fa-paper-plane ms-2"></i>
                </button>
            </form>
        </div>
    </div>
</div>


<script>

    // Function to handle the visual selection of a category card
    function select(element) {
        document.querySelectorAll('.cat-card').forEach(c => c.classList.remove('selected'));
        element.classList.add('selected');
        updateSubjectOptions(element.querySelector('input').value);
    }

    // Function to dynamically update the "Specific Concern Type" dropdown based on the selected category
    function updateSubjectOptions(category) {
        const subjectDropdown = document.querySelector('select[name="title"]');
        subjectDropdown.innerHTML = '<option value="" disabled selected> Select a subcategory </option>';

        let options = [];

        switch (category) {
            case 'Academic':
                options = [
                    'Grading Issue',
                    'Curriculum/Subject',
                    'Faculty Concern',
                    'Academic Records',
                    'Enrollment Issue',
                    'Other Academic'
                ];
                break;
            case 'Financial':
                options = [
                    'Tuition & Fees',
                    'Scholarship',
                    'Billing Error',
                    'Refund Request',
                    'Financial Aid',
                    'Other Financial'
                ];
                break;
            case 'Welfare':
                options = [
                    'Bullying / Harassment',
                    'Mental Health',
                    'Campus Safety',
                    'Facilities Issue',
                    'Medical concern',
                    'Other Welfare'
                ];
                break;
        }

        options.forEach(option => {
            const optionElement = document.createElement('option');
            optionElement.value = option;
            optionElement.textContent = option;
            subjectDropdown.appendChild(optionElement);
        });
    }
</script>
</body>
</html>