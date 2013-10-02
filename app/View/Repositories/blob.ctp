<?php
echo $this->Html->link(__('<< Back'), Router::url(array('controller' => 'repositories', 'action' => 'tree', 'repo_name' => $repo['Repository']['name'])
) . '/' . $parentTree . '/');?>

<h1><?php echo $repo['User']['username'];?> / <?php echo $repo['Repository']['name']; echo str_replace('/', ' / ', str_replace('trunk', '', $blobPath));?></h1>

<?php echo $fileContent;?>