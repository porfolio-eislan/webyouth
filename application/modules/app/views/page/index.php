<?php if(@$main == 'home') : ?>

<?= $this->load->view('content/home') ?>

<?php elseif(@$main == 'about') : ?>

<?= $this->load->view('content/about') ?>

<?php elseif(@$main == 'blog') : ?>

<?= $this->load->view('content/blog') ?>

<?php elseif(@$main == 'team') : ?>

<?= $this->load->view('content/team') ?>

<?php elseif(@$main == 'visi') : ?>

<?= $this->load->view('content/visi') ?>

<?php elseif(@$main == 'galeri') : ?>
    
<?= $this->load->view('content/galeri') ?>

<?php elseif(@$main == 'youth') : ?>

<?= $this->load->view('content/youth') ?>

<?php elseif(@$main == 'activity') : ?>
    
<?= $this->load->view('content/activity') ?>

<?php elseif(@$main == 'venue') : ?>

<?= $this->load->view('content/venue') ?>

<?php endif; ?>