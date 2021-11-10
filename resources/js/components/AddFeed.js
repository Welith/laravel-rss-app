import React, {Component} from "react";
import {Link} from 'react-router-dom';
import swal from 'sweetalert';


class AddFeed extends Component {

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

    saveFeed = async (e) => {

        e.preventDefault();

        const res = await axios.post('/api/feeds', this.state);

        if (res.data.status === 200) {

            swal({
                title: "Added!",
                text: res.data.message,
                icon: "success",
                button: "OK",
            }).then(function () {

                window.location = '/';
            });
        }  else if (res.data.stack === 400) {

            this.setState({

                error_list: res.data.message,
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
                    <div className="col-md-6">
                        <div className="card">
                            <div className="card-header text-center">
                                <h4>Add Feed
                                    <Link to={'/'} className="btn btn-primary btn-sm float-right"><i className="fas fa-long-arrow-alt-left"></i> Back</Link>
                                </h4>
                            </div>
                            <div className="card-body">
                                <form onSubmit={this.saveFeed}>
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
                                        <span className="text-danger">{this.state.error_list.description}</span>
                                    </div>
                                    <div className="col text-center">
                                        <button type="submit" className="btn btn-success"><i className="fas fa-save"></i> Save Feed</button>
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

export default AddFeed;


