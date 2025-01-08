
<!-- Team Start -->
        <div class="container-fluid team py-5">
            <div class="container pb-5">
                <div class="text-center mx-auto pb-5 wow fadeInUp" data-wow-delay="0.2s" style="max-width: 800px;">
                    <h4 class="text-primary">Perangkat Desa</h4>
                    <h1 class="display-4 mb-4">Jajaran Perangkat Desa <?= @$identitas['desa_nm'] ?></h1>
                    <p class="mb-0">Lorem ipsum dolor, sit amet consectetur adipisicing elit. Tenetur adipisci facilis cupiditate recusandae aperiam temporibus corporis itaque quis facere, numquam, ad culpa deserunt sint dolorem autem obcaecati, ipsam mollitia hic.
                    </p>
                </div>
                <div class="row g-4">
                    <?php foreach(@$perangkat_desa as $row) : ?>
                    <div class="col-md-6 col-lg-6 col-xl-3 wow fadeInUp" data-wow-delay="0.2s">
                        <div class="team-item">
                            <div class="team-img">
                                <img src="<?= base_url() ?>assets/img/team-1.jpg" class="img-fluid rounded-top w-100" alt="">
                                <div class="team-icon">
                                    <a class="btn btn-primary btn-sm-square rounded-pill mb-2" href=""><i class="fab fa-facebook-f"></i></a>
                                    <a class="btn btn-primary btn-sm-square rounded-pill mb-2" href=""><i class="fab fa-twitter"></i></a>
                                    <a class="btn btn-primary btn-sm-square rounded-pill mb-2" href=""><i class="fab fa-linkedin-in"></i></a>
                                    <a class="btn btn-primary btn-sm-square rounded-pill mb-0" href=""><i class="fab fa-instagram"></i></a>
                                </div>
                            </div>
                            <div class="team-title p-4" style="min-height: 130px;">
                                <h5 class="mb-0 text-white fw-bold"><?= $row['perangkatdesa_nm'] ?><?= $row['gelar_nm'] != '' ? ', ' . $row['gelar_nm'] : '' ?></h5>
                                <p class="mb-0 fs-6"><?= @$row['jabatan_nm'] ?></p>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <!-- <div class="row">
                    <div class="col-md-12 col-lg-12 col-xl-12 text-center wow fadeInUp" data-wow-delay="0.2s">
                        <a class="btn btn-primary rounded-pill py-3 px-5 mt-5 wow fadeInUp" data-wow-delay="0.2s" href="#">Selengkapnya Perangkat Desa</a>
                    </div>
                </div> -->
            </div>
        </div>
        <!-- Team End -->