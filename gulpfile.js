var gulp  = require('gulp'),
    watch = require('gulp-watch'),
    exec  = require('child_process').exec,
    fs    = require('fs');

var logFile = './source/_deploy.log';

state = 0;

var logToFile = (content) => {
  fs.appendFile(logFile, content + '\n', (fsError) => {
    console.error(fsError);
  });
}

gulp.task('generate', (done) => {
  exec('"hexo" g', (error, stdout, stderr) => {
    if (error) {
      console.error(`exec error: ${error}`);
    } else {
      let msg = 'Generation successful.';
      console.log(`stdout: ${stdout}`);
      console.log(`stderr: ${stderr}`);
      if (stderr) {
        // stderr is not empty. There is generation error.
        logToFile(stderr);
      } else {
        // generation successful.
        console.log(msg);
        logToFile(msg);
        state = 1;
      }
    }
    done();
  });
});

gulp.task('deploy', (done) => {
  if (state == 1) {
    exec('"hexo" d', (error, stdout, stderr) => {
      if (error) {
        console.error(`exec error: ${error}`);
        fs.writeFile(logFile, error, (fsError) => {
          console.error(fsError);
        });
      } else {
        let msg = 'Generation successful.';
        console.log(`stdout: ${stdout}`);
        console.log(`stderr: ${stderr}`);
        console.log(msg);
        logToFile(msg);
      }
      done();
    });
  } else {
    let msg = 'Generation unsuccessful. Will no deploy.';
    console.log(msg);
    logToFile(msg);
    done();
  };
});

gulp.task('clearLog', (done) => {
  fs.writeFile(logFile, '', () => {
    console.log('_deploy.log cleared.')
    done();
  });
});

gulp.task('watch', () => {
  gulp.watch('./source/_deploy.txt', gulp.series('clearLog', 'generate', 'deploy'));
});

gulp.task('default', gulp.series('watch'));

