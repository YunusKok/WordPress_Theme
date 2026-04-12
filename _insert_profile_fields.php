<?php
/**
 * Helper script to insert extended profile fields into template-dashboard.php
 */

$file = __DIR__ . '/template-dashboard.php';
$content = file_get_contents($file);

// The block to insert after the email field's closing </div> and before profile-response
$insert = '
					<?php
					// ── Extended Student/Tenant Fields ──
					if ( in_array( \'tenant\', (array) $user->roles, true ) ) :
						$u_nationality   = get_user_meta( $user->ID, \'_thessnest_nationality\', true );
						$u_sending_uni   = get_user_meta( $user->ID, \'_thessnest_sending_university\', true );
						$u_receiving_uni = get_user_meta( $user->ID, \'_thessnest_receiving_university\', true );
						$u_funding       = get_user_meta( $user->ID, \'_thessnest_funding_source\', true );
						$u_age           = get_user_meta( $user->ID, \'_thessnest_student_age\', true );
						$u_emergency     = get_user_meta( $user->ID, \'_thessnest_emergency_contact\', true );
						$u_passport      = function_exists( \'thessnest_get_passport_number\' ) ? thessnest_get_passport_number( $user->ID ) : \'\';
					?>
					<div style="margin-top:var(--space-4); padding-top:var(--space-4); border-top:1px solid var(--color-border);">
						<h3 style="font-size:var(--font-size-base); margin-bottom:var(--space-4); color:var(--color-primary);"><?php esc_html_e( \'Student Information\', \'thessnest\' ); ?></h3>
						<div style="display:grid; grid-template-columns:1fr 1fr; gap:var(--space-4); margin-bottom:var(--space-4);">
							<div>
								<label style="display:block; margin-bottom:4px; font-weight:600; font-size:var(--font-size-sm);"><?php esc_html_e( \'Nationality\', \'thessnest\' ); ?></label>
								<input type="text" name="thessnest_nationality" value="<?php echo esc_attr( $u_nationality ); ?>" placeholder="e.g. Turkish" style="width:100%; padding:var(--space-3); border:1px solid var(--color-border); border-radius:var(--radius-md); background:var(--color-surface); color:var(--color-text);">
							</div>
							<div>
								<label style="display:block; margin-bottom:4px; font-weight:600; font-size:var(--font-size-sm);"><?php esc_html_e( \'Age\', \'thessnest\' ); ?></label>
								<input type="number" name="thessnest_student_age" value="<?php echo esc_attr( $u_age ); ?>" min="16" max="99" style="width:100%; padding:var(--space-3); border:1px solid var(--color-border); border-radius:var(--radius-md); background:var(--color-surface); color:var(--color-text);">
							</div>
						</div>
						<div style="display:grid; grid-template-columns:1fr 1fr; gap:var(--space-4); margin-bottom:var(--space-4);">
							<div>
								<label style="display:block; margin-bottom:4px; font-weight:600; font-size:var(--font-size-sm);"><?php esc_html_e( \'Sending University\', \'thessnest\' ); ?></label>
								<input type="text" name="thessnest_sending_university" value="<?php echo esc_attr( $u_sending_uni ); ?>" placeholder="Your home university" style="width:100%; padding:var(--space-3); border:1px solid var(--color-border); border-radius:var(--radius-md); background:var(--color-surface); color:var(--color-text);">
							</div>
							<div>
								<label style="display:block; margin-bottom:4px; font-weight:600; font-size:var(--font-size-sm);"><?php esc_html_e( \'Receiving University\', \'thessnest\' ); ?></label>
								<input type="text" name="thessnest_receiving_university" value="<?php echo esc_attr( $u_receiving_uni ); ?>" placeholder="University in host country" style="width:100%; padding:var(--space-3); border:1px solid var(--color-border); border-radius:var(--radius-md); background:var(--color-surface); color:var(--color-text);">
							</div>
						</div>
						<div style="display:grid; grid-template-columns:1fr 1fr; gap:var(--space-4); margin-bottom:var(--space-4);">
							<div>
								<label style="display:block; margin-bottom:4px; font-weight:600; font-size:var(--font-size-sm);"><?php esc_html_e( \'Funding Source\', \'thessnest\' ); ?></label>
								<select name="thessnest_funding_source" style="width:100%; padding:var(--space-3); border:1px solid var(--color-border); border-radius:var(--radius-md); background:var(--color-surface); color:var(--color-text);">
									<option value=""><?php esc_html_e( \'— Select —\', \'thessnest\' ); ?></option>
									<option value="erasmus+" <?php selected( $u_funding, \'erasmus+\' ); ?>>Erasmus+</option>
									<option value="scholarship" <?php selected( $u_funding, \'scholarship\' ); ?>><?php esc_html_e( \'Scholarship\', \'thessnest\' ); ?></option>
									<option value="self_funded" <?php selected( $u_funding, \'self_funded\' ); ?>><?php esc_html_e( \'Self-Funded\', \'thessnest\' ); ?></option>
									<option value="other" <?php selected( $u_funding, \'other\' ); ?>><?php esc_html_e( \'Other\', \'thessnest\' ); ?></option>
								</select>
							</div>
							<div>
								<label style="display:block; margin-bottom:4px; font-weight:600; font-size:var(--font-size-sm);"><?php esc_html_e( \'Emergency Contact\', \'thessnest\' ); ?></label>
								<input type="text" name="thessnest_emergency_contact" value="<?php echo esc_attr( $u_emergency ); ?>" placeholder="Name & phone number" style="width:100%; padding:var(--space-3); border:1px solid var(--color-border); border-radius:var(--radius-md); background:var(--color-surface); color:var(--color-text);">
							</div>
						</div>
						<div style="margin-bottom:var(--space-4);">
							<label style="display:block; margin-bottom:4px; font-weight:600; font-size:var(--font-size-sm);">
								<?php esc_html_e( \'Passport Number\', \'thessnest\' ); ?>
								<span style="font-weight:400; color:var(--color-text-muted); font-size:12px;">(<?php esc_html_e( \'Encrypted & required for visa documents\', \'thessnest\' ); ?>)</span>
							</label>
							<input type="text" name="thessnest_passport_number" value="<?php echo esc_attr( $u_passport ); ?>" placeholder="e.g. U12345678" style="width:100%; max-width:300px; padding:var(--space-3); border:1px solid var(--color-border); border-radius:var(--radius-md); background:var(--color-surface); color:var(--color-text); font-family:monospace;">
						</div>
					</div>
					<?php endif; ?>

					<?php
					// ── Extended Host/Landlord Fields ──
					if ( in_array( \'landlord\', (array) $user->roles, true ) || in_array( \'administrator\', (array) $user->roles, true ) ) :
						$u_tax_id  = get_user_meta( $user->ID, \'_thessnest_tax_id\', true );
						$u_id_card = get_user_meta( $user->ID, \'_thessnest_id_card_number\', true );
					?>
					<div style="margin-top:var(--space-4); padding-top:var(--space-4); border-top:1px solid var(--color-border);">
						<h3 style="font-size:var(--font-size-base); margin-bottom:var(--space-4); color:var(--color-primary);"><?php esc_html_e( \'Host Identification\', \'thessnest\' ); ?></h3>
						<div style="display:grid; grid-template-columns:1fr 1fr; gap:var(--space-4); margin-bottom:var(--space-4);">
							<div>
								<label style="display:block; margin-bottom:4px; font-weight:600; font-size:var(--font-size-sm);"><?php esc_html_e( \'Tax ID (AFM)\', \'thessnest\' ); ?></label>
								<input type="text" name="thessnest_tax_id" value="<?php echo esc_attr( $u_tax_id ); ?>" placeholder="e.g. 123456789" style="width:100%; padding:var(--space-3); border:1px solid var(--color-border); border-radius:var(--radius-md); background:var(--color-surface); color:var(--color-text);">
							</div>
							<div>
								<label style="display:block; margin-bottom:4px; font-weight:600; font-size:var(--font-size-sm);"><?php esc_html_e( \'ID Card Number\', \'thessnest\' ); ?></label>
								<input type="text" name="thessnest_id_card_number" value="<?php echo esc_attr( $u_id_card ); ?>" placeholder="National ID number" style="width:100%; padding:var(--space-3); border:1px solid var(--color-border); border-radius:var(--radius-md); background:var(--color-surface); color:var(--color-text);">
							</div>
						</div>
					</div>
					<?php endif; ?>
';

// Find the target: after line "margin-bottom:var(--space-6);">...email...</div>" and before "profile-response"
$target = '<div id="profile-response"';
$pos = strpos($content, $target);

if ($pos === false) {
    echo "ERROR: Could not find profile-response div\n";
    exit(1);
}

// Find the blank line before profile-response (go back from $pos)
$lineStart = strrpos($content, "\n", $pos - strlen($content));

// Insert the new content before the profile-response div
$newContent = substr($content, 0, $lineStart) . $insert . "\n" . substr($content, $lineStart + 1);

file_put_contents($file, $newContent);
echo "SUCCESS: Extended profile fields inserted at position $pos\n";
echo "File size: " . filesize($file) . " bytes\n";
