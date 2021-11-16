import React, {Component} from "react";
import {Link, useHistory} from 'react-router-dom';
import swal from "sweetalert";

class EditFeed extends Component {


    feed_id = this.props.match.params.id;

    state = {
        title: '',
        link: '',
        source: '',
        source_url: '',
        publish_date: '',
        description: '',
        error_list: []
    }

    handleInput = (e) => {

        this.setState({

            [e.target.name]: e.target.value
        });
    }

    async componentDidMount(){

        const res = await axios.get(`/api/feeds/${this.feed_id}`)

        if (res.data.status === 200) {

            let date = new Date(res.data.feed.publish_date)

            this.setState({

                title: res.data.feed.title,
                link: res.data.feed.link,
                source: res.data.feed.source,
                source_url: res.data.feed.source_url,
                publish_date: (date.toISOString()).slice(0, -5),
                description: res.data.feed.description
            });
        } else {

            swal({
                title: "Error!",
                text: res.data.message,
                icon: "error",
                button: "OK",
            });
        }
    }

    updateFeed = async (e) => {

        e.preventDefault();

        const res = await axios.put(`/api/feeds/${this.feed_id}/edit`, this.state).catch(function (err) {

            if (err) {
                swal({
                    title: "Error!",
                    text: "Internal Server Error!",
                    icon: "error",
                    button: "OK",
                })
            }
        });

        if (res.data.status === 200) {

            swal({

                title: "Updated!",
                text: res.data.message,
                icon: "success",
                button: "OK",
            }).then(function () {

                window.location = "/"
            });
        } else if (res.data.status === 400) {

            this.setState({

                error_list: res.data.message
            })
        } else {

            swal({
                title: "Error!",
                text: res.data.message,
                icon: "error",
                button: "OK",
            })
        }
    }

    render() {

        return(
            <div className="container align-items-center justify-content-center">
                <div className="row align-items-center justify-content-center">
                    <div className="col-md-6 align-items-center justify-content-center">
                        <div className="card">
                            <div className="card-header text-center">
                                <h4>Edit Feed
                                    <Link to={{pathname: '/', state: this.props.location.state}} className="btn btn-primary btn-sm float-right"><i className="fas fa-long-arrow-alt-left"></i> Back</Link>
                                </h4>
                            </div>
                            <div className="card-body">
                                <form onSubmit={this.updateFeed}>
                                    <div className="form-group mb-3">
                                        <label>Title</label>
                                        <input type="text" name="title" value={this.state.title} onChange={this.handleInput} className="form-control"/>
                                        <span className="text-danger">{this.state.error_list.title}</span>
                                    </div>
                                    <div className="form-group mb-3">
                                        <label>Link</label>
                                        <input type="text" name="link" onChange={this.handleInput} value={this.state.link} className="form-control"/>
                                        <span className="text-danger">{this.state.error_list.link}</span>
                                    </div>
                                    <div className="form-group mb-3">
                                        <label>Source</label>
                                        <input type="text" name="source" onChange={this.handleInput} value={this.state.source} className="form-control"/>
                                        <span className="text-danger">{this.state.error_list.source}</span>
                                    </div>
                                    <div className="form-group mb-3">
                                        <label>Source URL</label>
                                        <input type="text" name="source_url" onChange={this.handleInput} value={this.state.source_url} className="form-control"/>
                                        <span className="text-danger">{this.state.error_list.source_url}</span>
                                    </div>
                                    <div className="form-group mb-3">
                                        <label>Publish Date</label>
                                        <input type="datetime-local" name="publish_date" onChange={this.handleInput} value={this.state.publish_date} className="form-control"/>
                                        <span className="text-danger">{this.state.error_list.publish_date}</span>
                                    </div>
                                    <div className="form-group mb-3">
                                        <label>Description</label>
                                        <textarea name="description" onChange={this.handleInput} value={this.state.description} className="form-control"/>
                                    </div>
                                    <div className="col text-center">
                                        <button type="submit" className="btn btn-success"><i className="fas fa-save"></i> Update Feed</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        );
    }
}

export default EditFeed;


