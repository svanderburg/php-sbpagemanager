{composerEnv, fetchurl, fetchgit ? null, fetchhg ? null, fetchsvn ? null, noDev ? false}:

let
  packages = {
    "svanderburg/php-sbcrud" = {
      targetDir = "";
      src = fetchgit {
        name = "svanderburg-php-sbcrud-e11a0b8089ac56176dbe3363aa5e4be6e269025f";
        url = "https://github.com/svanderburg/php-sbcrud.git";
        rev = "e11a0b8089ac56176dbe3363aa5e4be6e269025f";
        sha256 = "1v0ywl1qlim7vzc388dmhkn44bi4vcyfahlnmwfnq9gqkbr3qwz6";
      };
    };
    "svanderburg/php-sbdata" = {
      targetDir = "";
      src = fetchgit {
        name = "svanderburg-php-sbdata-cbe47d7b902779decd54399dac570beb1e75373a";
        url = "https://github.com/svanderburg/php-sbdata.git";
        rev = "cbe47d7b902779decd54399dac570beb1e75373a";
        sha256 = "09kk38vccjwp7bdz1qh7czgf6ik6rxhzhv57x4ipz44jism9ly21";
      };
    };
    "svanderburg/php-sbeditor" = {
      targetDir = "";
      src = fetchgit {
        name = "svanderburg-php-sbeditor-d2c558323e1317fe0f216301ef38dc4e27f29f91";
        url = "https://github.com/svanderburg/php-sbeditor.git";
        rev = "d2c558323e1317fe0f216301ef38dc4e27f29f91";
        sha256 = "0xld6fq3j07nxf0wvafh5y51994w9awj7ysjz5aap4haqxkyncck";
      };
    };
    "svanderburg/php-sbgallery" = {
      targetDir = "";
      src = fetchgit {
        name = "svanderburg-php-sbgallery-a76428fdd2c63b2f5dbd63e16c152516f5fedc66";
        url = "https://github.com/svanderburg/php-sbgallery.git";
        rev = "a76428fdd2c63b2f5dbd63e16c152516f5fedc66";
        sha256 = "0im8pkwg14qhcj45himagi7vy5xm4c0ykac14h0ygsd11ppils7h";
      };
    };
    "svanderburg/php-sblayout" = {
      targetDir = "";
      src = fetchgit {
        name = "svanderburg-php-sblayout-4904b4ed5fbbd3050ad312c922431ea37154e9e3";
        url = "https://github.com/svanderburg/php-sblayout.git";
        rev = "4904b4ed5fbbd3050ad312c922431ea37154e9e3";
        sha256 = "0vh68xr3ga15krzjymvcmm1wwjx0v81w62wp9gm4g0zc4x890bsf";
      };
    };
  };
  devPackages = {};
in
composerEnv.buildPackage {
  inherit packages devPackages noDev;
  name = "svanderburg-php-sbpagemanager";
  src = ./.;
  executable = false;
  symlinkDependencies = false;
  meta = {
    license = "Apache-2.0";
  };
}
