<?php
/**
 * The file that defines the core plugin class
 *
 * @link       https://posimyth.com/
 * @since      1.0.0
 *
 * @package    Uichemy
 */

/** If this file is called directly, abort. */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$current_token = apply_filters( 'uich_manage_token', 'get_token' );

?>
<div class='flex flex-col items-center justify-between gap-3
	-ml-5 bg-white
	font-inter text-black'>
	<style>
		@import url('https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap');
	</style>

	<!-- Header -->
	<div class="
		flex items-center gap-x-10 gap-y-3
		bg-secondary w-full p-10
		flex-wrap justify-center sm:justify-between
	">
		<img class="scale-75 sm:scale-100" src="<?php echo esc_url( UICH_URL ); ?>assets/svg/uichemy-logo.svg" />

		<div class="bg-white rounded-full py-2 px-4 font-bold scale-75 sm:scale-100">
			<?php echo esc_html__( 'Version ', 'uichemy' ) . esc_html( UICH_VERSION ); ?>
		</div>
	</div>

	<!-- Content -->
	<div class="flex flex-col gap-4 items-stretch w-[70%] sm:w-96">

		<img class="h-40 w-40 self-center m-8 sm:m-0" src="<?php echo esc_url( UICH_URL ); ?>assets/svg/lock-vector.svg" />

		<div class="flex flex-col w-full gap-2">

			<div class="text-sm font-medium"><?php echo esc_html__( 'Site URL', 'uichemy' ); ?></div>

			<div class="flex itemc-center justify-between p-2 pl-4 rounded-[10px] border border-neutral-300 cursor-text">

				<input readonly
					id="uichemy-site-url-input"
					value="<?php echo esc_url( site_url() ); ?>"
					class="!bg-transparent !outline-none !text-sm !text-black/60 !grow"
				/>

				<button id="uichemy-url-copy-btn" class="bg-neutral-100 hover:bg-neutral-200 p-1 ml-2 rounded-md">
					<img class="copy-icon" src="<?php echo esc_url( UICH_URL ); ?>assets/svg//copy-action.svg" />
					<img class="hidden done-icon" src="<?php echo esc_url( UICH_URL ); ?>assets/svg//done-status.svg" />
				</button>
			</div>
		</div>

		<div class="flex flex-col w-full gap-2">
			<div class="text-sm font-medium">
				<?php echo esc_html__( 'Security Token', 'uichemy' ); ?>
			</div>

			<div class="flex itemc-center justify-between p-2 pl-4 rounded-[10px] border border-neutral-300 cursor-text">

				<input 
					readonly
					id="uichemy-token-input"
					value="<?php echo esc_attr( $current_token ); ?>"
					class="!bg-transparent !outline-none !text-sm !text-black/60 !flex-grow"
				/>

				<button id="uichemy-token-copy-btn" class="bg-neutral-100 hover:bg-neutral-200 p-1 ml-2 rounded-md">
					<img class="copy-icon" src="<?php echo esc_url( UICH_URL ); ?>assets/svg/copy-action.svg" />
					<img class="hidden done-icon" src="<?php echo esc_url( UICH_URL ); ?>assets/svg/done-status.svg" />
				</button>
			</div>
		</div>

		<button
			id="uichemy-regenerate-btn"
			class="p-3 rounded-xl
				bg-secondary hover:bg-secondary-dark text-white
				transition-colors duration-150 
				font-semibold text-base">
				<?php echo esc_html__( 'Regenerate Token', 'uichemy' ); ?>
		</button>
	</div>

	<!-- Footer -->
	<div class="py-10 lg:pt-10 px-6 flex gap-x-4 gap-y-3 sm:gap-x-5 flex-wrap items-center justify-center">
		<?php
			$social_links = array(
				'BLOG'     => array( 'blog.svg', esc_url( 'https://uichemy.com/blogs/' ) ),
				'DOCS'     => array( 'docs.svg', esc_url( 'https://uichemy.com/docs' ) ),
				'FACEBOOK' => array( 'facebook.svg', esc_url( 'https://www.facebook.com/uichemy/' ) ),
				'HOME'     => array( 'home.svg', esc_url( 'https://uichemy.com' ) ),
				'TWITTER'  => array( 'twitter.svg', esc_url( 'https://twitter.com/uichemy' ) ),
			);

			foreach ( $social_links as $social_name => $val ) {
				echo "<a href='" . esc_url( $val[1] ) . "' target='_blank'  rel='noopener noreferrer'>
                    <button class='border border-secondary/10 p-3 rounded-lg
                        hover:bg-neutral-50 text-black
                        flex gap-2 items-center justify-center
                        min-w-[9rem]'>
                        <img src=" . esc_url( UICH_URL ) . 'assets/svg/footer-icons/' . esc_attr( $val[0] ) . ' />' . esc_html( $social_name ) . '
                    </button>
                </a>';
			}
			?>
	</div>
</div>
