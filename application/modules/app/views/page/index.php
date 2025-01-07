<?php if(@$main == 'home') : ?>

<?= $this->load->view('content/home') ?>

<?php elseif(@$main == 'about') : ?>

    <?= $this->load->view('content/about') ?>

<?php endif; ?>