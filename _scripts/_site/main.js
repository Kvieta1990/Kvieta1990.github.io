var fs = require("q-io/fs"),
    q = require("q"),
    git = require("gift-wrapper"),
    generate = require("./generate.js"),
    exec = require('child_process').exec;

var repoDir = __dirname + "/../"
var credentialFilePath = repoDir + ".git/credentials";
var repo = git(repoDir);

function setAuthConfig(){
  var deferred = q.defer();
  exec('git config credential.helper "store --file=' + credentialFilePath + '"', 
    {
        cwd: repoDir,
        encoding: "utf8",
        maxBuffer: 5000 * 1024
    },
    function(err){
      if(err){
        return deferred.reject(err);
      }
      
      return deferred.resolve();
    }
  );
  
  return deferred.promise;
};

function publishChanges(gitlab_personal_access_token){
  return setAuthConfig()
  .then(function(){
    var content = "https://ouath2:" + gitlab_personal_access_token + "@code.ornl.gov/general/rmcprofile.git"
    return fs.write(credentialFilePath, content);
  })
  .then(function(){
    return repo.identify({name: "y8z", email: "zhangy3@ornl.gov"});
  })
  .then(function(){
    console.log("Adding all files...");
    return repo.add("-A");
  })  
  .then(function(){
    console.log("Committing changes...");
    return repo.commit("[ci skip] automated comment issue creation and tag+archive page generation");
  })
  .then(function(){
    console.log("Pushing changes...");
    return repo.remote_push("--force --quiet origin", "HEAD:master");
  })
  .then(function(){
    console.log("Pushed successfully.");
    return repo.status().then(console.log);
  })
};


// the main entry point
generate()
// .then(function(){
//     return ghi_create(process.env.GH_TOKEN);
// })
.then(function(){
  return repo.status();
})
.then(function(status){
  if(!status.clean){
    return publishChanges(process.env.GITLAB_TOKEN);
  }
})
.fail(console.error);
