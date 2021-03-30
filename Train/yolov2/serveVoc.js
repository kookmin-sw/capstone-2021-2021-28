const express = require('express')
const path = require('path')
const fs = require('fs')

const app = express()

app.use(express.json())
app.use(express.urlencoded({ extended: true }))

const public = path.join(__dirname, './yolov2')
app.use(express.static(public))
app.use(express.static(path.join(__dirname, './js')))
app.use(express.static(path.join(__dirname, '../examples/public')))
app.use(express.static(path.join(__dirname, '../models')))
app.use(express.static(path.join(__dirname, '../dist')))
app.use(express.static(path.join(__dirname, './node_modules/file-saver')))
app.use(express.static(path.join(__dirname, './temp')))

//app.get('/', (req, res) => res.redirect('./train'))
//app.get('./train', (req, res) => res.sendFile(path.join(public, 'index.html')))
//
//
//const trainDataPath = path.resolve('./train')
//app.use(express.static(trainDataPath))


app.listen(3000, () => console.log('Listening on port 3000!'))

