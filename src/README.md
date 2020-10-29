if you want to run unit tests put in your `package.json`

```
  "autoload":{
    "psr-4":{
      "mongodb\\":"src/common/extensions/mongodb"
    }
  },
```
and then run `composer dumpautoload`