        <!-- Header Start -->
        <div class="container-fluid bg-breadcrumb">
            <div class="container text-center py-5" style="max-width: 900px;">
                <h4 class="text-white display-4 mb-4 wow fadeInDown" data-wow-delay="0.1s">
                    <?php if(@$url == 'about') : ?>
                        Tentang Kami
                    <?php elseif(@$url == 'blog') : ?>
                        Berita
                    <?php elseif(@$url == 'team') : ?>
                        Perangkat Desa
                    <?php elseif(@$url == 'visi') : ?>
                        Visi & Misi
                    <?php elseif(@$url == 'galeri') : ?>
                        Foto & Video
                    <?php elseif(@$url == 'youth') : ?>
                        Karang Taruna
                    <?php elseif(@$url == 'activity') : ?>
                        Kegiatan
                    <?php elseif(@$url == 'venue') : ?>
                        Wisata
                    <?php endif; ?>                  </h4>
                <ol class="breadcrumb d-flex justify-content-center mb-0 wow fadeInDown" data-wow-delay="0.3s">
                    <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                    <li class="breadcrumb-item"><a href="#">Page</a></li>
                    <li class="breadcrumb-item"><a href="#" class="text-capitalize">
                        <?php if(@$url == 'about') : ?>
                            Tentang Kami
                        <?php elseif(@$url == 'blog') : ?>
                            Berita
                        <?php elseif(@$url == 'team') : ?>
                            Perangkat Desa
                        <?php elseif(@$url == 'visi') : ?>
                            Visi & Misi
                        <?php elseif(@$url == 'galeri') : ?>
                            Foto & Video
                        <?php elseif(@$url == 'youth') : ?>
                            Karang Taruna
                        <?php elseif(@$url == 'activity') : ?>
                            Kegiatan
                        <?php elseif(@$url == 'venue') : ?>
                            Wisata
                        <?php endif; ?>                    
                    </a>
                    </li>
                </ol>    
            </div>
        </div>
        <!-- Header End -->