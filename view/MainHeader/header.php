<header class="site-header">
	<div class="container-fluid">

		<a href="#" class="site-logo">
			<img class="hidden-md-down" src="../../public/img/logo-2.png" alt="">
			<img class="hidden-lg-up" src="../../public/img/logo-2-mob.png" alt="">
		</a>

		<button id="show-hide-sidebar-toggle" class="show-hide-sidebar">
			<span>toggle menu</span>
		</button>

		<button class="hamburger hamburger--htla">
			<span>toggle menu</span>
		</button>
		<div class="site-header-content">
			<div class="site-header-content-in">
				<div class="site-header-shown">
					<div class="dropdown dropdown-notification notif">
						<a href="#"
							class="header-alarm dropdown-toggle active"
							id="dd-notification"
							data-toggle="dropdown"
							aria-haspopup="true"
							aria-expanded="false">
							<i class="font-icon-alarm"></i>
						</a>
						<div class="dropdown-menu dropdown-menu-right dropdown-menu-notif" aria-labelledby="dd-notification">
							<div class="dropdown-menu-notif-header">
								Notifications
								<span class="label label-pill label-danger">4</span>
							</div>
							<div class="dropdown-menu-notif-list">
								<div class="dropdown-menu-notif-item">
									<div class="photo">
										<img src="../../public/img/photo-64-1.jpg" alt="">
									</div>
									<div class="dot"></div>
									<a href="#">Morgan</a> was bothering about something
									<div class="color-blue-grey-lighter">7 hours ago</div>
								</div>
								<div class="dropdown-menu-notif-item">
									<div class="photo">
										<img src="../../public/img/photo-64-2.jpg" alt="">
									</div>
									<div class="dot"></div>
									<a href="#">Lioneli</a> had commented on this <a href="#">Super Important Thing</a>
									<div class="color-blue-grey-lighter">7 hours ago</div>
								</div>
								<div class="dropdown-menu-notif-item">
									<div class="photo">
										<img src="../../public/img/photo-64-3.jpg" alt="">
									</div>
									<div class="dot"></div>
									<a href="#">Xavier</a> had commented on the <a href="#">Movie title</a>
									<div class="color-blue-grey-lighter">7 hours ago</div>
								</div>
								<div class="dropdown-menu-notif-item">
									<div class="photo">
										<img src="../../public/img/photo-64-4.jpg" alt="">
									</div>
									<a href="#">Lionely</a> wants to go to <a href="#">Cinema</a> with you to see <a href="#">This Movie</a>
									<div class="color-blue-grey-lighter">7 hours ago</div>
								</div>
							</div>
							<div class="dropdown-menu-notif-more">
								<a href="#">See more</a>
							</div>
						</div>
					</div>

					<div class="dropdown dropdown-notification messages">
						<a href="#"
							class="header-alarm dropdown-toggle active"
							id="dd-messages"
							data-toggle="dropdown"
							aria-haspopup="true"
							aria-expanded="false">
							<i class="font-icon-mail"></i>
						</a>
					</div>

					<input type="hidden" id="user_idx" value="<?php echo $_SESSION["usu_id"] ?>">
					<input type="hidden" id="rol_idx" value="<?php echo $_SESSION["rol_id"] ?>">


					<div class="dropdown user-menu">
						<button class="dropdown-toggle" id="dd-user-menu" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<span><?php echo $_SESSION["usu_nom"] ?> <?php echo $_SESSION["usu_ape"] ?></span>
							<img src="../../public/img/user-<?php echo $_SESSION["rol_id"] ?>.png" alt="">
						</button>
						<div class="dropdown-menu dropdown-menu-right" aria-labelledby="dd-user-menu">
							<a class="dropdown-item" href="../../view/Perfil"><span class="font-icon glyphicon glyphicon-user"></span>Perfil</a>
							<a class="dropdown-item" href="#"><span class="font-icon glyphicon glyphicon-cog"></span>Ayuda</a>
							<div class="dropdown-divider"></div>
							<a class="dropdown-item" href="../Logout/logout.php"><span class="font-icon glyphicon glyphicon-log-out"></span>Cerrar sesion</a>
						</div>
					</div>

					<button type="button" class="burger-right">
						<i class="font-icon-menu-addl"></i>
					</button>
				</div><!--.site-header-shown-->

				<div class="mobile-menu-right-overlay"></div>
			</div><!--site-header-content-in-->
		</div><!--.site-header-content-->
	</div><!--.container-fluid-->
</header><!--.site-header-->