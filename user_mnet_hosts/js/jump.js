/**
 * Jump
 *
 * This javascript function will redirect the user towards the selected platform
 * Its goal is to 'mask' the jump URL to the user
 *
 * @param root The current platform adress.
 * @param The platform number to jump to
 */

function jump(root,platform) {
    
    window.location=root+"/auth/mnet/jump.php?hostid="+platform;
    
}