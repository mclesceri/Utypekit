<?php
if(
	!defined('CP_CALCULATEDFIELDSF_DISCOUNT_CODES_TABLE_NAME') ||
	!defined('CP_CALCULATEDFIELDSF_DEFAULT_CURRENCY')
)
{
	_e( 'Direct access not allowed.', 'calculated-fields-form' );
    exit;
}

if(!class_exists( 'CPCFF_COUPON' ))
{
	class CPCFF_COUPON
	{
		public static $coupon_applied = false;
		public static $discount_note  = '';

		public static function apply_discount($formid, $couponcode, $price)
		{
			global $wpdb;
			$coupon = self::get_coupon($formid, $couponcode);

			if (!empty($coupon))
			{
				self::$coupon_applied = $coupon;
				$coupon->discount = preg_replace('/[^\.\d]/', '', $coupon->discount);

				if ($coupon->availability==1)
				{
					$price = number_format (floatval ($price) - $coupon->discount,2);
					self::$discount_note = " (".cp_calculatedfieldsf_get_option('currency', CP_CALCULATEDFIELDSF_DEFAULT_CURRENCY)." ".$coupon->discount." discount applied)";
				}
				else
				{
					$price = number_format (floatval ($price) - $price*$coupon->discount/100,2);
					self::$discount_note = " (".$coupon->discount."% discount applied)";
				}
			}

			return $price;
		} // End apply_discount

		public static function active_coupons( $formid )
		{
			global $wpdb;
			$coupons = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ".CP_CALCULATEDFIELDSF_DISCOUNT_CODES_TABLE_NAME." WHERE expires>='".date("Y-m-d")." 00:00:00' AND `form_id`=%d", $formid ) );

			return (!empty($coupons)) ? $coupons : 0;
		} // End active_coupons

		public static function get_coupon($formid, $couponcode)
		{
			global $wpdb;
			$coupon = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM ".CP_CALCULATEDFIELDSF_DISCOUNT_CODES_TABLE_NAME." WHERE code=%s AND expires>='".date("Y-m-d")." 00:00:00' AND `form_id`=%d", $couponcode, $formid ) );

			if (!empty($coupon)) return $coupon;

			return false;
		} // End get_coupon

		public static function add_coupon($formid, $couponcode, $discount, $discounttype, $expires)
		{
			global $wpdb;
			return $wpdb->insert( CP_CALCULATEDFIELDSF_DISCOUNT_CODES_TABLE_NAME,
							array('form_id' => $formid,
									'code' => $couponcode,
									'discount' => $discount,
									'availability' => $discounttype,
									'expires' => $expires,
							),
							array( '%d', '%s', '%s', '%d', '%s' )
				);
		} // End add_coupon

		public static function delete_coupon($id)
		{
			global $wpdb;
			return $wpdb->query( $wpdb->prepare( "DELETE FROM ".CP_CALCULATEDFIELDSF_DISCOUNT_CODES_TABLE_NAME." WHERE id = %d", $id ));
		} // End delete_coupon

		public static function display_codes($formid)
		{
			global $wpdb;

			$codes = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM '.CP_CALCULATEDFIELDSF_DISCOUNT_CODES_TABLE_NAME.' WHERE `form_id`=%d', $formid ) );

			$result  = '';
			if (count ($codes))
			{
				$result .= '<table><tr><th style="padding:2px;background-color: #cccccc;font-weight:bold;">'.__('Cupon Code', 'calculated-fields-form' ).'</th><th style="padding:2px;background-color: #cccccc;font-weight:bold;">'.__('Discount', 'calculated-fields-form' ).'</th><th style="padding:2px;background-color: #cccccc;font-weight:bold;">'.__('Type', 'calculated-fields-form' ).'</th><th style="padding:2px;background-color: #cccccc;font-weight:bold;">'.__('Valid until', 'calculated-fields-form' ).'</th><th style="padding:2px;background-color: #cccccc;font-weight:bold;">'.__('Options', 'calculated-fields-form' ).'</th></tr>';

				foreach ($codes as $value)
				{
				   $result .= '<tr>';
				   $result .= '<td>'.$value->code.'</td>';
				   $result .= '<td>'.$value->discount.'</td>';
				   $result .= '<td>'.($value->availability==1? __("Fixed Value", 'calculated-fields-form' ):__("Percent", 'calculated-fields-form' )).'</td>';
				   $result .= '<td>'.substr($value->expires,0,10).'</td>';
				   $result .= '<td>[<a href="javascript:dex_delete_coupon('.$value->id.')">'.__('Delete', 'calculated-fields-form' ).'</a>]</td>';
				   $result .= '</tr>';
				}
				$result .= '</table>';
			}
			else
				$result .= __( 'No discount codes listed for this form yet.', 'calculated-fields-form' );

			return $result;
		} // End display_codes

	} // End Class
}
?>