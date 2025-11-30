<?php
require '../config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . SITE_URL . '/login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Diagnostics</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Upload System Diagnostics</h1>
        
        <div class="card mt-4">
            <div class="card-header">
                <h5>Configuration</h5>
            </div>
            <div class="card-body">
                <table class="table">
                    <tr>
                        <td><strong>SITE_URL:</strong></td>
                        <td><code><?php echo SITE_URL; ?></code></td>
                    </tr>
                    <tr>
                        <td><strong>__DIR__:</strong></td>
                        <td><code><?php echo __DIR__; ?></code></td>
                    </tr>
                    <tr>
                        <td><strong>dirname(__DIR__):</strong></td>
                        <td><code><?php echo dirname(__DIR__); ?></code></td>
                    </tr>
                    <tr>
                        <td><strong>Uploads folder path:</strong></td>
                        <td><code><?php echo dirname(__DIR__) . '/uploads'; ?></code></td>
                    </tr>
                    <tr>
                        <td><strong>Expected image URL:</strong></td>
                        <td><code><?php echo SITE_URL . '/uploads/img_example.jpg'; ?></code></td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h5>Directory Check</h5>
            </div>
            <div class="card-body">
                <table class="table">
                    <tr>
                        <td><strong>Uploads directory exists:</strong></td>
                        <td>
                            <?php 
                            $uploads_dir = dirname(__DIR__) . '/uploads';
                            if (is_dir($uploads_dir)) {
                                echo '<span class="badge bg-success">YES</span>';
                            } else {
                                echo '<span class="badge bg-danger">NO</span>';
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Uploads directory writable:</strong></td>
                        <td>
                            <?php 
                            if (is_writable($uploads_dir)) {
                                echo '<span class="badge bg-success">YES</span>';
                            } else {
                                echo '<span class="badge bg-danger">NO</span>';
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Files in uploads:</strong></td>
                        <td>
                            <?php 
                            if (is_dir($uploads_dir)) {
                                $files = array_diff(scandir($uploads_dir), ['.', '..']);
                                echo count($files) . ' files';
                            } else {
                                echo 'N/A';
                            }
                            ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h5>Sample Images in Database</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Featured Image URL</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $result = $conn->query("SELECT id, title, featured_image_url FROM portfolio_items LIMIT 5");
                        while ($row = $result->fetch_assoc()):
                        ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo htmlspecialchars($row['title']); ?></td>
                                <td><code style="font-size: 0.8rem;"><?php echo htmlspecialchars($row['featured_image_url']); ?></code></td>
                                <td>
                                    <?php
                                    if (!empty($row['featured_image_url'])) {
                                        // Extract filename from URL
                                        $filename = basename($row['featured_image_url']);
                                        $file_path = dirname(__DIR__) . '/uploads/' . $filename;
                                      l>

</htm
</body>t>ripjs"></scle.min..bundstrapist/js/boot/dp@5.3.0ootstranet/npm/br.delivs://cdn.js"http<script src=v>

        </di   </div>
a>
     oard</ to Dashb">Backtn-secondaryss="btn bhp" cla/dashboard.p/adminE_URL; ?>echo SIT?php <a href="<           
 t-4 mb-4">="m class        <div
div>

        </</div>      
         </ul>             >
code></limain.com</s://doode>httpshould be <cTE_URL SIde> → m</comain.cohttps://doi><code>       <l             de></li>
colocalhost</ttp://d be <code>hTE_URL shoul/code> → SI//localhost<de>http:     <li><co         i>
      o</code></ltfoliormy-plocalhost/p://ode>httbe <chould  SITE_URL s> →dertfolio</cohost/my-poocal>http://lli><code          <          <ul>
         p>
       ></ at:</stronge is sitrong>If yourt-3"><stclass="mp          <      pre>

   </             └── ...
 here)
tored mages ss/  (iploaddes/
├── unclu├── idmin/
 URL)
├── asite actual atch yourRL should mITE_U
├── .env (Sct_root/jero<pre>
p             
   /p>e:</strong><e should bstructury orur directtrong>Yo3"><smt-<p class="                  
       ol>
       </             li>
    SITE_URL</fter fixingoad images a-upl   <li>Re               
  </li>dinglyRL accor.env SITE_Uate  updare wrong,If URLs      <li>           /li>
    tructure< directory sour actualURLs match ythe erify   <li>V              </li>
    vection aboabase" sen Datple Images ie "Samheck th       <li>C       
             <ol>
         ong></p>ying:</strispla d are not images>Ifp><strong           <  body">
   card-div class="    <        </div>
     >
       </h5dationsenecomm       <h5>R    
     der">hea"card-<div class=            ">
rd mt-4="calass   <div c
     iv>
</d      /div>
             <
 if; ?>nd e      <?php    
      /p>st.<to tease  databinimages ted">No xt-mu="te  <p class          ?>
        e: ls <?php e    
           ll></p>smaong.</RL is wr icon, the Un imageokeou see a brf ys correct. I URL iove, thee ab an imagseef you small>I3"><s="mt-as   <p cl            ">
     lid #ccc;der: 2px so00px; bor: 3max-widthstyle=""Test" ; ?>" alt=rs($url)alchaho htmlspeciec="<?php <img src              
      </p>strong>ng>Image:</tro   <p><s               /p>
  ><</coders($url); ?>lchaciaecho htmlspecode><?php ng> <roL:</sting URestp><strong>T          <     ?>
                    l'];
 ur_image_['featured = $rowrl $u               ;
    h_assoc()->fetclt$resu  $row =              0):
      s >m_row($result->nu  if             );
  "'' LIMIT 1rl != image_ued_aturLL AND feOT NUS Ne_url Ied_imagHERE featurtems Wlio_ifoM portmage_url FROeatured_iSELECT fquery("nn->$co $result =                
 <?php           dy">
    bolass="card-     <div c>
         </div         5>
 lay</hispTest Image D  <h5>    
          r">ard-headeass="c<div cl   
         mt-4">"card v class=      <didiv>

  
        </>       </divle>
     ab</t           body>
         </t                while; ?>
   <?php end                   >
      </tr                       
       </td>                           ?>
                                     }
                                pan>';
  RL</sary">No Undsecobadge bg-n class="paho '<s        ec                               else {
        }                               }
                                 ;
      ing</span>'>File miss-danger" bglass="badgean c<sp 'echo                                        
    e {ls   } e                               n>';
      /spats<">File exisg-success bass="badgean cl '<sp       echo                                     ath)) {
ts($file_pisif (file_ex                                   
       