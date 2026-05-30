<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo isset($page_title) ? $page_title : 'HostelPro'; ?></title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">

<?php
if (!isset($page) || $page === '') {
    $page = pathinfo($_SERVER['PHP_SELF'] ?? '', PATHINFO_FILENAME);
}
?>

<link rel="stylesheet" href="../../../public/css/landlord/common.css">
<?php if (!empty($page)): ?>
    <link rel="stylesheet" href="../../../public/css/landlord/<?php echo $page; ?>.css">
<?php endif; ?>