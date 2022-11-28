{composerEnv, fetchurl, fetchgit ? null, fetchhg ? null, fetchsvn ? null, noDev ? false}:

let
  packages = {
    "svanderburg/php-sbcrud" = {
      targetDir = "";
      src = fetchgit {
        name = "svanderburg-php-sbcrud-6f9574b313f0452b2f74222a621238ab67ee1984";
        url = "https://github.com/svanderburg/php-sbcrud.git";
        rev = "6f9574b313f0452b2f74222a621238ab67ee1984";
        sha256 = "1bsfxcka7x7pac84ajz7pjz0n77qgkxm899zm7pa6061icwkamf5";
      };
    };
    "svanderburg/php-sbdata" = {
      targetDir = "";
      src = fetchgit {
        name = "svanderburg-php-sbdata-ee777698c697ddee23e060db9621487022f76c11";
        url = "https://github.com/svanderburg/php-sbdata.git";
        rev = "ee777698c697ddee23e060db9621487022f76c11";
        sha256 = "0l878g61d8kqmp3inwglxv2c50c6r0dmf4gi0p2n5ww59m3f29vx";
      };
    };
    "svanderburg/php-sbeditor" = {
      targetDir = "";
      src = fetchgit {
        name = "svanderburg-php-sbeditor-a231deb1f13ddc4aa40147385065da3a92ad3fe3";
        url = "https://github.com/svanderburg/php-sbeditor.git";
        rev = "a231deb1f13ddc4aa40147385065da3a92ad3fe3";
        sha256 = "19775i4q8gw1ah0zdl9vwpb67kbihjjcmq54ff1xrdp3ly1mhb56";
      };
    };
    "svanderburg/php-sbgallery" = {
      targetDir = "";
      src = fetchgit {
        name = "svanderburg-php-sbgallery-56c0b930f7e8132a57c033690960300c348b2c01";
        url = "https://github.com/svanderburg/php-sbgallery.git";
        rev = "56c0b930f7e8132a57c033690960300c348b2c01";
        sha256 = "0pj8cfnqp80rj2jq36zzkybn5mnaw1x0kv2wi0cgrc4ka0a608lv";
      };
    };
    "svanderburg/php-sblayout" = {
      targetDir = "";
      src = fetchgit {
        name = "svanderburg-php-sblayout-1cf019759fed392d2a75e2caf5e5a929d7668267";
        url = "https://github.com/svanderburg/php-sblayout.git";
        rev = "1cf019759fed392d2a75e2caf5e5a929d7668267";
        sha256 = "0mr5v8jkkpksn6cvsswglyynkmdgqmc8gff2hi5a01i8wp5k60sx";
      };
    };
  };
  devPackages = {};
in
composerEnv.buildPackage {
  inherit packages devPackages noDev;
  name = "svanderburg-php-sbpagemanager";
  src = composerEnv.filterSrc ./.;
  executable = false;
  symlinkDependencies = false;
  meta = {
    license = "Apache-2.0";
  };
}
