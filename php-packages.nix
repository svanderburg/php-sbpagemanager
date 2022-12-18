{composerEnv, fetchurl, fetchgit ? null, fetchhg ? null, fetchsvn ? null, noDev ? false}:

let
  packages = {
    "svanderburg/php-sbcrud" = {
      targetDir = "";
      src = fetchgit {
        name = "svanderburg-php-sbcrud-25a22f640f11276b758efc5609027545576b659c";
        url = "https://github.com/svanderburg/php-sbcrud.git";
        rev = "25a22f640f11276b758efc5609027545576b659c";
        sha256 = "15a2q7hghabh07b5hjj32sw6x2q4md0j8svc4fmcrvvw31ck66wn";
      };
    };
    "svanderburg/php-sbdata" = {
      targetDir = "";
      src = fetchgit {
        name = "svanderburg-php-sbdata-0a9c962dcb92dfade5015c43637b8ab8800f52c3";
        url = "https://github.com/svanderburg/php-sbdata.git";
        rev = "0a9c962dcb92dfade5015c43637b8ab8800f52c3";
        sha256 = "0v5s2w9l8dcxk70m8v3mqad2505i7h9xlzb659p1b9csjyr6d21v";
      };
    };
    "svanderburg/php-sbeditor" = {
      targetDir = "";
      src = fetchgit {
        name = "svanderburg-php-sbeditor-0d9bdb175e7f6d52809b0b56c812fe11ecc5dd41";
        url = "https://github.com/svanderburg/php-sbeditor.git";
        rev = "0d9bdb175e7f6d52809b0b56c812fe11ecc5dd41";
        sha256 = "15anbhaqcmvn44akxv00q82gwsd2c59c72phahsc1snqsf3n47hn";
      };
    };
    "svanderburg/php-sbgallery" = {
      targetDir = "";
      src = fetchgit {
        name = "svanderburg-php-sbgallery-b240922db78cbc8d2fbe4dfca8396b7278a86532";
        url = "https://github.com/svanderburg/php-sbgallery.git";
        rev = "b240922db78cbc8d2fbe4dfca8396b7278a86532";
        sha256 = "095449xzvwh89sfhn5yb8lg5fysk021576v4blq9zylcjr402gk7";
      };
    };
    "svanderburg/php-sblayout" = {
      targetDir = "";
      src = fetchgit {
        name = "svanderburg-php-sblayout-97546d499170396598338a62bc2043fb84ef24c1";
        url = "https://github.com/svanderburg/php-sblayout.git";
        rev = "97546d499170396598338a62bc2043fb84ef24c1";
        sha256 = "0qzh5yznqndalsd91pc1rdgla4a59y7ga72xcwnl1q4gyl83wmlg";
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
