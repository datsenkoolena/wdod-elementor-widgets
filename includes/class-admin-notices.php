<?php
/**
 * Admin notices shown when the Elementor widgets cannot be registered.
 *
 * @package WDOD\ElementorWidgets
 */

namespace WDOD\ElementorWidgets;

defined( 'ABSPATH' ) || exit;

/**
 * Class Admin_Notices.
 *
 * Renders a dismissible notice with contextual install / activate / update
 * links. The dismissal is stored per user (user meta) and keyed by the
 * reason, so a new problem (e.g. an outdated Elementor after installing it)
 * shows the notice again.
 */
class Admin_Notices {

	/**
	 * User meta key storing the dismissed reason.
	 *
	 * @var string
	 */
	const META_KEY = 'wdod_ew_dismissed_notice';

	/**
	 * Nonce action for the dismiss link.
	 *
	 * @var string
	 */
	const NONCE_ACTION = 'wdod_ew_dismiss_notice';

	/**
	 * Query argument that triggers the dismissal.
	 *
	 * @var string
	 */
	const QUERY_ARG = 'wdod_ew_dismiss';

	/**
	 * Elementor plugin basename.
	 *
	 * @var string
	 */
	const ELEMENTOR_BASENAME = 'elementor/elementor.php';

	/**
	 * Why the widgets are unavailable: 'php', 'missing' or 'version'.
	 *
	 * @var string
	 */
	private $reason;

	/**
	 * Constructor.
	 *
	 * @param string $reason Reason code returned by Plugin::check_requirements().
	 */
	public function __construct( $reason ) {
		$this->reason = (string) $reason;

		add_action( 'admin_init', array( $this, 'maybe_dismiss' ) );
		add_action( 'admin_notices', array( $this, 'render' ) );
	}

	/**
	 * Handles the nonce-protected dismiss link.
	 *
	 * @return void
	 */
	public function maybe_dismiss() {
		if ( ! isset( $_GET[ self::QUERY_ARG ], $_GET['_wpnonce'] ) ) {
			return;
		}

		$nonce = sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) );

		if ( ! wp_verify_nonce( $nonce, self::NONCE_ACTION ) ) {
			return;
		}

		$reason = sanitize_key( wp_unslash( $_GET[ self::QUERY_ARG ] ) );

		update_user_meta( get_current_user_id(), self::META_KEY, $reason );

		wp_safe_redirect( remove_query_arg( array( self::QUERY_ARG, '_wpnonce' ) ) );
		exit;
	}

	/**
	 * Whether the current user dismissed the notice for the current reason.
	 *
	 * @return bool
	 */
	private function is_dismissed() {
		return get_user_meta( get_current_user_id(), self::META_KEY, true ) === $this->reason;
	}

	/**
	 * Whether the current user may act on the notice at all.
	 *
	 * @return bool
	 */
	private function user_can_see() {
		return current_user_can( 'activate_plugins' ) || current_user_can( 'install_plugins' );
	}

	/**
	 * Builds the dismiss URL.
	 *
	 * @return string
	 */
	private function dismiss_url() {
		return wp_nonce_url(
			add_query_arg( self::QUERY_ARG, $this->reason ),
			self::NONCE_ACTION
		);
	}

	/**
	 * Builds the "install Elementor" URL.
	 *
	 * @return string
	 */
	private function install_url() {
		return wp_nonce_url(
			self_admin_url( 'update.php?action=install-plugin&plugin=elementor' ),
			'install-plugin_elementor'
		);
	}

	/**
	 * Builds the "activate Elementor" URL.
	 *
	 * @return string
	 */
	private function activate_url() {
		return wp_nonce_url(
			self_admin_url( 'plugins.php?action=activate&plugin=' . self::ELEMENTOR_BASENAME ),
			'activate-plugin_' . self::ELEMENTOR_BASENAME
		);
	}

	/**
	 * Whether Elementor exists on disk (installed but maybe inactive).
	 *
	 * @return bool
	 */
	private function is_elementor_installed() {
		return file_exists( WP_PLUGIN_DIR . '/' . self::ELEMENTOR_BASENAME );
	}

	/**
	 * Returns the notice message and the action link for the current reason.
	 *
	 * @return array{message:string,action_url:string,action_label:string}
	 */
	private function get_content() {
		$content = array(
			'message'      => '',
			'action_url'   => '',
			'action_label' => '',
		);

		switch ( $this->reason ) {
			case 'php':
				$content['message'] = sprintf(
					/* translators: 1: required PHP version, 2: current PHP version. */
					esc_html__( 'WDOD Elementor Widgets requires PHP %1$s or newer. Your server runs PHP %2$s, so the Elementor widgets are disabled. The shortcodes keep working.', 'wdod-elementor-widgets' ),
					WDOD_EW_MIN_PHP,
					PHP_VERSION
				);
				break;

			case 'version':
				$content['message'] = sprintf(
					/* translators: 1: required Elementor version, 2: installed Elementor version. */
					esc_html__( 'WDOD Elementor Widgets requires Elementor %1$s or newer. You have %2$s installed, so the widgets are not registered until Elementor is updated.', 'wdod-elementor-widgets' ),
					WDOD_EW_MIN_ELEMENTOR,
					defined( 'ELEMENTOR_VERSION' ) ? ELEMENTOR_VERSION : '0'
				);

				if ( current_user_can( 'update_plugins' ) ) {
					$content['action_url']   = self_admin_url( 'plugins.php?plugin_status=upgrade' );
					$content['action_label'] = esc_html__( 'Update Elementor', 'wdod-elementor-widgets' );
				}
				break;

			case 'missing':
			default:
				if ( $this->is_elementor_installed() ) {
					$content['message'] = esc_html__( 'WDOD Elementor Widgets works best with Elementor, which is installed but not active. Until it is activated only the shortcodes are available.', 'wdod-elementor-widgets' );

					if ( current_user_can( 'activate_plugins' ) ) {
						$content['action_url']   = $this->activate_url();
						$content['action_label'] = esc_html__( 'Activate Elementor', 'wdod-elementor-widgets' );
					}
				} else {
					$content['message'] = esc_html__( 'WDOD Elementor Widgets works best with Elementor, which is not installed. Until it is installed only the shortcodes are available.', 'wdod-elementor-widgets' );

					if ( current_user_can( 'install_plugins' ) ) {
						$content['action_url']   = $this->install_url();
						$content['action_label'] = esc_html__( 'Install Elementor', 'wdod-elementor-widgets' );
					}
				}
				break;
		}

		return $content;
	}

	/**
	 * Prints the notice.
	 *
	 * @return void
	 */
	public function render() {
		if ( ! $this->user_can_see() || $this->is_dismissed() ) {
			return;
		}

		$content = $this->get_content();
		?>
		<div class="notice notice-warning wdod-ew-notice">
			<p>
				<strong><?php esc_html_e( 'WDOD Elementor Widgets', 'wdod-elementor-widgets' ); ?>:</strong>
				<?php echo esc_html( $content['message'] ); ?>
			</p>
			<p>
				<?php if ( '' !== $content['action_url'] ) : ?>
					<a class="button button-primary" href="<?php echo esc_url( $content['action_url'] ); ?>">
						<?php echo esc_html( $content['action_label'] ); ?>
					</a>
				<?php endif; ?>
				<a class="button button-link" href="<?php echo esc_url( $this->dismiss_url() ); ?>">
					<?php esc_html_e( 'Dismiss this notice', 'wdod-elementor-widgets' ); ?>
				</a>
			</p>
		</div>
		<?php
	}
}
