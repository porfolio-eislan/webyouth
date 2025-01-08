

        <!-- Navbar & Hero Start -->
        <div class="container-fluid nav-bar px-0 px-lg-4 py-lg-0">
            <div class="container">
                <nav class="navbar navbar-expand-lg navbar-light"> 
                    <a href="<?= $this->uri . 'index/home'. '?' . $uri ?>" class="navbar-brand p-0">
                        <h1 class="text-primary mb-0"><i class="fab fa-slack me-2"></i> <?= $identitas['desa_nm'] ?></h1>
                        <!-- <img src="<?= base_url() ?>assets/img/logo.png" alt="Logo"> -->
                    </a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                        <span class="fa fa-bars"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarCollapse">
                        <div class="navbar-nav mx-0 mx-lg-auto">
                            <a href="<?= $this->uri . 'index/home'. '?' . $uri ?>" class="nav-item nav-link <?= $url == 'home' ? 'active' : '' ?>">Home</a>
                            <a href="<?= $this->uri . 'index/about'. '?' . $uri ?>" class="nav-item nav-link <?= $url == 'about' ? 'active' : '' ?>">Tentang</a>
                            <a href="<?= $this->uri . 'index/blog'. '?' . $uri ?>" class="nav-item nav-link <?= $url == 'blog' ? 'active' : '' ?>">Berita</a>
                            <!-- <a href="service.html" class="nav-item nav-link">Services</a> -->
                            <div class="nav-item dropdown">
                                <a class="nav-link" data-bs-toggle="dropdown">
                                    <span class="dropdown-toggle">Pages</span>
                                </a>
                                <div class="dropdown-menu">
                                    <a href="<?= $this->uri . 'index/team'. '?' . $uri ?>" class="dropdown-item <?= $url == 'team' ? 'active' : '' ?>">Perangkat Desa</a>
                                    <a href="<?= $this->uri . 'index/visi'. '?' . $uri ?>" class="dropdown-item <?= $url == 'visi' ? 'active' : '' ?>">Visi & Misi</a>
                                    <a href="<?= $this->uri . 'index/galeri'. '?' . $uri ?>" class="dropdown-item <?= $url == 'galeri' ? 'active' : '' ?>">Foto & Video</a>
                                </div>
                            </div>
                            <a href="<?= $this->uri . 'index/youth'. '?' . $uri ?>" class="nav-item nav-link <?= $url == 'youth' ? 'active' : '' ?>">Karang&nbsp;Taruna</a>
                            <a href="<?= $this->uri . 'index/activity'. '?' . $uri ?>" class="nav-item nav-link <?= $url == 'activity' ? 'active' : '' ?>">Kegiatan</a>
                            <a href="<?= $this->uri . 'index/venue'. '?' . $uri ?>" class="nav-item nav-link <?= $url == 'venue' ? 'active' : '' ?>">Wisata</a>
                            <a href="contact.html" class="nav-item nav-link">Contact</a>
                            <div class="nav-btn px-3">
                                <button class="btn-search btn btn-primary btn-md-square flex-shrink-0" data-bs-toggle="modal" data-bs-target="#searchModal"><i class="fas fa-search"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="d-none d-xl-flex flex-shrink-0 ps-4">
                        <a href="#" class="btn btn-light btn-lg-square rounded-circle position-relative wow tada" data-wow-delay=".9s">
                            <i class="fa fa-phone-alt fa-2x"></i>
                            <div class="position-absolute" style="top: 7px; right: 12px;">
                                <span><i class="fa fa-comment-dots text-secondary"></i></span>
                            </div>
                        </a>
                        <div class="d-flex flex-column ms-3">
                            <span>Call to Our Experts</span>
                            <a href="tel:+ 0123 456 7890"><span class="text-dark">Free: + 0123 456 7890</span></a>
                        </div>
                    </div>
                </nav>
            </div>
        </div>
        <!-- Navbar & Hero End -->

        <!-- Modal Search Start -->
        <div class="modal fade" id="searchModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-fullscreen">
                <div class="modal-content rounded-0">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Search by keyword</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body d-flex align-items-center bg-primary">
                        <div class="input-group w-75 mx-auto d-flex">
                            <input type="search" class="form-control p-3" placeholder="keywords" aria-describedby="search-icon-1">
                            <span id="search-icon-1" class="btn bg-light border nput-group-text p-3"><i class="fa fa-search"></i></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal Search End -->