<?php
require '../config.php';
require '../includes/upload.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . SITE_URL . '/login.php');
    exit;
}

$page_title = 'Manage Portfolio';
$message = '';

// Delete portfolio item
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $item = $conn->query("SELECT featured_image_filename FROM portfolio_items WHERE id = $id")->fetch_assoc();
    if ($item && $item['featured_image_filename']) {
        deleteImage($item['featured_image_filename'], '../uploads');
    }
    // Delete associated images
    $images = $conn->query("SELECT image_filename FROM portfolio_images WHERE portfolio_id = $id");
    while ($img = $images->fetch_assoc()) {
        if ($img['image_filename']) {
            deleteImage($img['image_filename'], '../uploads');
        }
    }
    $conn->query("DELETE FROM portfolio_items WHERE id = $id");
    $message = '<div class="alert alert-success">Portfolio item deleted.</div>';
}

// Delete portfolio image
if (isset($_GET['delete_image'])) {
    $image_id = intval($_GET['delete_image']);
    $image = $conn->query("SELECT image_filename, portfolio_id FROM portfolio_images WHERE id = $image_id")->fetch_assoc();
    if ($image && $image['image_filename']) {
        deleteImage($image['image_filename'], '../uploads');
    }
    $conn->query("DELETE FROM portfolio_images WHERE id = $image_id");
    $message = '<div class="alert alert-success">Image deleted.</div>';
}

// Add/Edit portfolio item
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_item') {
    $title = $conn->real_escape_string($_POST['title']);
    $description = $conn->real_escape_string($_POST['description']);
    $body = $conn->real_escape_string($_POST['body']);
    $category = $conn->real_escape_string($_POST['category']);
    $link = $conn->real_escape_string($_POST['link']);
    $featured_image_url = $conn->real_escape_string($_POST['featured_image_url']);
    $featured_image_filename = '';
    
    // Handle featured image upload
    if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === 0) {
        $upload = uploadImage($_FILES['featured_image'], '../uploads');
        if ($upload['success']) {
            $featured_image_filename = $upload['filename'];
            $featured_image_url = $upload['url'];
        } else {
            $message = '<div class="alert alert-danger">' . $upload['message'] . '</div>';
        }
    }
    
    if (isset($_POST['id']) && $_POST['id']) {
        $id = intval($_POST['id']);
        if ($featured_image_filename) {
            $conn->query("UPDATE portfolio_items SET title='$title', description='$description', body='$body', category='$category', link='$link', featured_image_url='$featured_image_url', featured_image_filename='$featured_image_filename' WHERE id=$id");
        } else {
            $conn->query("UPDATE portfolio_items SET title='$title', description='$description', body='$body', category='$category', link='$link', featured_image_url='$featured_image_url' WHERE id=$id");
        }
        $message = '<div class="alert alert-success">Portfolio item updated.</div>';
    } else {
        $conn->query("INSERT INTO portfolio_items (title, description, body, category, link, featured_image_url, featured_image_filename) VALUES ('$title', '$description', '$body', '$category', '$link', '$featured_image_url', '$featured_image_filename')");
        $message = '<div class="alert alert-success">Portfolio item added.</div>';
    }
}

// Add portfolio image
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_image') {
    $portfolio_id = intval($_POST['portfolio_id']);
    $alt_text = $conn->real_escape_string($_POST['alt_text']);
    $image_url = '';
    $image_filename = '';
    
    if (isset($_FILES['portfolio_image']) && $_FILES['portfolio_image']['error'] === 0) {
        $upload = uploadImage($_FILES['portfolio_image'], '../uploads');
        if ($upload['success']) {
            $image_filename = $upload['filename'];
            $image_url = $upload['url'];
            
            $sort_order = $conn->query("SELECT MAX(sort_order) as max_order FROM portfolio_images WHERE portfolio_id = $portfolio_id")->fetch_assoc()['max_order'];
            $sort_order = ($sort_order === null) ? 0 : $sort_order + 1;
            
            $conn->query("INSERT INTO portfolio_images (portfolio_id, image_url, image_filename, alt_text, sort_order) VALUES ($portfolio_id, '$image_url', '$image_filename', '$alt_text', $sort_order)");
            $message = '<div class="alert alert-success">Image added to portfolio.</div>';
        } else {
            $message = '<div class="alert alert-danger">' . $upload['message'] . '</div>';
        }
    }
}

$edit_item = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $edit_item = $conn->query("SELECT * FROM portfolio_items WHERE id = $id")->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/admin.css">
    <style>
        .ql-container { font-size: 16px; min-height: 300px; }
        .ql-editor { min-height: 300px; }
        .portfolio-image-preview { max-width: 100px; height: auto; border-radius: 5px; }
        .image-gallery { display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 10px; margin-top: 15px; }
        .image-item { position: relative; border-radius: 5px; overflow: hidden; }
        .image-item img { width: 100%; height: 120px; object-fit: cover; }
        .image-item .delete-btn { position: absolute; top: 5px; right: 5px; background: rgba(255,0,0,0.8); color: white; border: none; border-radius: 3px; padding: 2px 6px; cursor: pointer; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-2 d-md-block bg-dark sidebar">
                <div class="position-sticky pt-3">
                    <h5 class="text-white px-3 mb-4">Admin Panel</h5>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo SITE_URL; ?>/admin/dashboard.php">
                                <i class="fas fa-chart-line"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="<?php echo SITE_URL; ?>/admin/portfolio.php">
                                <i class="fas fa-briefcase"></i> Portfolio
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo SITE_URL; ?>/admin/services.php">
                                <i class="fas fa-cogs"></i> Services
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo SITE_URL; ?>/admin/about.php">
                                <i class="fas fa-user"></i> About
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo SITE_URL; ?>/admin/messages.php">
                                <i class="fas fa-envelope"></i> Messages
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo SITE_URL; ?>/admin/social.php">
                                <i class="fas fa-share-alt"></i> Social Links
                            </a>
                        </li>
                        <li class="nav-item mt-4">
                            <a class="nav-link" href="<?php echo SITE_URL; ?>/logout.php">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="col-md-10 ms-sm-auto px-md-4">
                <div class="d-flex justify-content-between align-items-center py-4 border-bottom">
                    <h1>Manage Portfolio</h1>
                </div>

                <?php echo $message; ?>

                <div class="row mt-4">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h5><?php echo $edit_item ? 'Edit Portfolio Item' : 'Add New Portfolio Item'; ?></h5>
                            </div>
                            <div class="card-body">
                                <form method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="action" value="save_item">
                                    <?php if ($edit_item): ?>
                                        <input type="hidden" name="id" value="<?php echo $edit_item['id']; ?>">
                                    <?php endif; ?>
                                    
                                    <div class="mb-3">
                                        <label for="title" class="form-label">Title</label>
                                        <input type="text" class="form-control" id="title" name="title" value="<?php echo $edit_item ? htmlspecialchars($edit_item['title']) : ''; ?>" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="description" class="form-label">Short Description</label>
                                        <textarea class="form-control" id="description" name="description" rows="2" required><?php echo $edit_item ? htmlspecialchars($edit_item['description']) : ''; ?></textarea>
                                        <small class="text-muted">This appears on the portfolio list page</small>
                                    </div>

                                    <div class="mb-3">
                                        <label for="body" class="form-label">Full Description (Rich Text)</label>
                                        <div id="editor" style="background: white;"></div>
                                        <textarea id="body" name="body" style="display:none;"><?php echo $edit_item ? htmlspecialchars($edit_item['body']) : ''; ?></textarea>
                                        <small class="text-muted">This appears on the project detail page</small>
                                    </div>

                                    <div class="mb-3">
                                        <label for="category" class="form-label">Category</label>
                                        <input type="text" class="form-control" id="category" name="category" value="<?php echo $edit_item ? htmlspecialchars($edit_item['category']) : ''; ?>">
                                    </div>

                                    <div class="mb-3">
                                        <label for="link" class="form-label">Project Link</label>
                                        <input type="url" class="form-control" id="link" name="link" value="<?php echo $edit_item ? htmlspecialchars($edit_item['link']) : ''; ?>">
                                    </div>

                                    <div class="mb-3">
                                        <label for="featured_image" class="form-label">Featured Image (Displayed on Portfolio List)</label>
                                        <input type="file" class="form-control" id="featured_image" name="featured_image" accept="image/*">
                                        <small class="text-muted">Max 5MB. Recommended: 600x400px</small>
                                        <?php if ($edit_item && $edit_item['featured_image_url']): ?>
                                            <div class="mt-2">
                                                <img src="<?php echo htmlspecialchars($edit_item['featured_image_url']); ?>" alt="Featured" class="portfolio-image-preview">
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <div class="mb-3">
                                        <label for="featured_image_url" class="form-label">Or Featured Image URL</label>
                                        <input type="url" class="form-control" id="featured_image_url" name="featured_image_url" value="<?php echo $edit_item ? htmlspecialchars($edit_item['featured_image_url']) : ''; ?>" placeholder="https://...">
                                    </div>

                                    <button type="submit" class="btn btn-primary"><?php echo $edit_item ? 'Update' : 'Add'; ?></button>
                                    <?php if ($edit_item): ?>
                                        <a href="<?php echo SITE_URL; ?>/admin/portfolio.php" class="btn btn-secondary">Cancel</a>
                                    <?php endif; ?>
                                </form>
                            </div>
                        </div>

                        <!-- Add Images to Portfolio -->
                        <?php if ($edit_item): ?>
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5>Portfolio Images Gallery</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="action" value="add_image">
                                    <input type="hidden" name="portfolio_id" value="<?php echo $edit_item['id']; ?>">
                                    
                                    <div class="mb-3">
                                        <label for="portfolio_image" class="form-label">Add Image to Gallery</label>
                                        <input type="file" class="form-control" id="portfolio_image" name="portfolio_image" accept="image/*" required>
                                        <small class="text-muted">Max 5MB. Recommended: 800x600px</small>
                                    </div>

                                    <div class="mb-3">
                                        <label for="alt_text" class="form-label">Alt Text (for accessibility)</label>
                                        
l>y>
</htmbodript>
</</sc
     });L;
       erHTMll.root.innvalue = quiody').ById('blementtEdocument.ge     ) {
       function(r('submit', entListene').addEvctor('formrySelement.que   docu    ion
 ssubmi sre formfoarea be hidden text   // Update   f; ?>

  ndi?php e     <); ?>;
   'body']_item[edit_encode($ json?php echoHTML = <nnerot.irouill.        q
y']): ?>item['bodit_em && $edt_it if ($edihp      <?pntent
  sting co// Load exi  

              });    }

          ]              'clean']
     [           eo'],
    ide', 'vink', 'imag       ['l          ' }],
   'bullet{ 'list': , ed'}st': 'order 'li  [{               ock'],
    'code-blte',lockquo      ['b         '],
     strikeine', 'c', 'underl, 'itali   ['bold'            ,
     3, false] }]: [1, 2, eader'     [{ 'h         
      r: [  toolba          : {
        modules        ow',
e: 'snhem         ttor', {
   di Quill('#el = new var quil
        editorlize Quill/ Initia        /ipt>

    <scr"></script>se.min.jstrap.bundlist/js/boot@5.3.0/drapstt/npm/bootjsdelivr.ne://cdn. src="httpsript>

    <scdiv
    </v>     </diin>
           </ma>
    /div <              iv>
 /d <                   
 </div>                   
    </div>                         e>
   tabl  </                        
      y>od        </tb                   
         dwhile; ?>    <?php en                              >
      tr    </                                 td>
          </                                            
 ">Delete</a>Delete?')rn confirm('click="retuger" onm btn-dantn-sbtn blass="']; ?>" co $item['id ech?php?delete=<a href="     <                                           
    >Edit</a>-warning"m btn"btn btn-ss= ?>" clasd'];em['icho $it<?php e"?edit=<a href=                                                   d>
 <t                                              /td>
  ']); ?><goryaters($item['cmlspecialchaphp echo ht<td><?                                                >
</td 15)); ?>tle'], 0,tir($item['chars(substlspecialhp echo htm<td><?p                                            >
          <tr                                 
             ?>                                _assoc()):
->fetch$result ($item = le    whi                                 C");
   t DEScreated_aORDER BY items o_tfoliporROM  * FELECT->query("S $connesult =     $r                        
           ?php  <                             >
            <tbody                           >
         </thead                            >
     </tr                                        th>
>Actions</  <th                                        ory</th>
  eg<th>Cat                                         
   itle</th>>Tth           <                             tr>
            <                              thead>
          <                       ">
     ble-smable talass="table c    <t                        ">
    auto;-y:  overflow 600px;-height:yle="maxbody" stass="card-  <div cl                     
         </div>              
           Items</h5>5>Portfolio       <h                      ">
   rd-headerass="cacl    <div                         ">
="cardlass      <div c              
    d-4">"col-mclass=div    <                 >

  </div              >
    p endif; ?  <?ph                      </div>
              
          v>        </di                    if; ?>
 <?php end                             </div>
                                  ?>
 dwhile;en <?php                             v>
       di    </                           
     ete</a>>Del image?')"lete thisconfirm('Deturn k="re" onclicelete-btn"d class=?>"id']; mg['o $ich<?php eete_image=href="?del   <a                                     ]); ?>">
 t_text'g['al($imharslclspecia htm<?php echo>" alt="; ?])age_url'rs($img['imialchatmlspec echo h src="<?php      <img                            ">
      e-itemss="imagclaiv  <d                                  : ?>
 oc())s->fetch_ass= $imagewhile ($img hp ?p        <                         
   gallery">s="image-   <div clas                    >
                  ?                       ws > 0):
>num_ro$images-      if (                     ;
     rder")ort_oR BY s. " ORDE['id'] $edit_item" . = d  portfolio_is WHEREimagetfolio_M porFROSELECT * ("conn->query $images = $                          <?php
                                  ery -->
    Gallayspl- Di      <!-                    

      /form>         <                  >
     mage</buttonAdd Iccess">"btn btn-sut" class=ubmi="son type <butt                                   </div>

                                
    the image">"Describe older=xt" placeh="alt_te" nametext" id="alt_ntrols="form-coext" claspe="t<input ty